<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use App\Domain\CentralDomain\Billing\Constants\InvoiceMessages;
use App\Domain\CentralDomain\Billing\Constants\InvoiceStatus;
use App\Domain\CentralDomain\Billing\Constants\PaymentMethodType;
use Domain\CentralDomain\Billing\DataTransferObjects\BoletoPaymentData;
use Domain\CentralDomain\Billing\DataTransferObjects\PayInvoiceData;
use Domain\CentralDomain\Billing\DataTransferObjects\PixPaymentData;
use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PaymentGateway\StripeRepository;

class PayInvoiceAction
{
    public function __construct(
        private StripeRepositoryInterface $stripeRepository
    ) {}

    /**
     * Execute invoice payment
     *
     * @return BoletoPaymentData|PixPaymentData
     *
     * @throws GeneralExceptions
     */
    public function execute(
        string $stripeCustomerId,
        string $invoiceId,
        PayInvoiceData $payInvoiceData
    ): BoletoPaymentData|PixPaymentData {
        $invoice = $this->stripeRepository->getInvoice($invoiceId);

        if (! $invoice) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_INVOICE_NOT_FOUND, 404);
        }

        if ($invoice->status === InvoiceStatus::PAID) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_INVOICE_ALREADY_PAID, 400);
        }

        if ($invoice->status === InvoiceStatus::VOID) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_INVOICE_VOIDED, 400);
        }

        if (! PaymentMethodType::isValidForInvoicePayment($payInvoiceData->paymentMethod)) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_INVALID_PAYMENT_METHOD, 400);
        }

        if ($payInvoiceData->paymentMethod === PaymentMethodType::BOLETO) {
            return $this->payWithBoleto($stripeCustomerId, $invoiceId, $invoice->amount, $payInvoiceData);
        }

        return $this->payWithPix($stripeCustomerId, $invoiceId, $invoice->amount);
    }

    /**
     * Process Boleto payment
     *
     * @throws GeneralExceptions
     */
    private function payWithBoleto(
        string $stripeCustomerId,
        string $invoiceId,
        int $amount,
        PayInvoiceData $payInvoiceData
    ): BoletoPaymentData {
        if (empty($payInvoiceData->taxId)) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_MISSING_TAX_ID, 400);
        }

        if (empty($payInvoiceData->addressLine1)) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_MISSING_BILLING_ADDRESS, 400);
        }

        $params = [
            StripeRepository::AMOUNT_KEY => $amount,
            StripeRepository::CUSTOMER_KEY => $stripeCustomerId,
            StripeRepository::INVOICE_ID_KEY => $invoiceId,
            StripeRepository::TAX_ID_KEY => $payInvoiceData->taxId,
            StripeRepository::NAME_KEY => $payInvoiceData->name,
            StripeRepository::EMAIL_KEY => $payInvoiceData->email,
            StripeRepository::ADDRESS_KEY => [
                StripeRepository::LINE1_KEY => $payInvoiceData->addressLine1,
                StripeRepository::CITY_KEY => $payInvoiceData->addressCity,
                StripeRepository::STATE_KEY => $payInvoiceData->addressState,
                StripeRepository::POSTAL_CODE_KEY => $payInvoiceData->addressPostalCode,
            ],
        ];

        $result = $this->stripeRepository->createBoletoPaymentIntent($params);

        if (! $result) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_CREATE_PAYMENT_INTENT, 500);
        }

        return $result;
    }

    /**
     * Process PIX payment
     *
     * @throws GeneralExceptions
     */
    private function payWithPix(
        string $stripeCustomerId,
        string $invoiceId,
        int $amount
    ): PixPaymentData {
        $params = [
            StripeRepository::AMOUNT_KEY => $amount,
            StripeRepository::CUSTOMER_KEY => $stripeCustomerId,
            StripeRepository::INVOICE_ID_KEY => $invoiceId,
        ];

        $result = $this->stripeRepository->createPixPaymentIntent($params);

        if (! $result) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_CREATE_PAYMENT_INTENT, 500);
        }

        return $result;
    }
}
