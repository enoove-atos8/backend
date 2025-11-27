<?php

namespace Infrastructure\Repositories\CentralDomain\PaymentGateway;

use App\Domain\CentralDomain\Billing\Constants\InvoiceMessages;
use App\Domain\CentralDomain\Billing\Constants\InvoiceStatus;
use App\Domain\CentralDomain\Billing\Constants\PaymentMethodType;
use Domain\CentralDomain\Billing\DataTransferObjects\BoletoPaymentData;
use Domain\CentralDomain\Billing\DataTransferObjects\InvoiceData;
use Domain\CentralDomain\Billing\DataTransferObjects\PixPaymentData;
use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Illuminate\Support\Collection;
use Stripe\StripeClient;

class StripeRepository implements StripeRepositoryInterface
{
    private StripeClient $stripe;

    const SUBSCRIPTION_KEY = 'subscription';

    const PAYMENT_METHOD_KEY = 'payment_method';

    const ID_KEY = 'id';

    const STATUS_KEY = 'status';

    const CURRENT_PERIOD_END_KEY = 'current_period_end';

    const CURRENT_PERIOD_START_KEY = 'current_period_start';

    const TRIAL_END_KEY = 'trial_end';

    const DEFAULT_PAYMENT_METHOD_KEY = 'default_payment_method';

    const CUSTOMER_KEY = 'customer';

    const TYPE_KEY = 'type';

    const BRAND_KEY = 'brand';

    const LAST4_KEY = 'last4';

    const EXP_MONTH_KEY = 'exp_month';

    const EXP_YEAR_KEY = 'exp_year';

    const NAME_KEY = 'name';

    const EMAIL_KEY = 'email';

    const PHONE_KEY = 'phone';

    const METADATA_KEY = 'metadata';

    const LIMIT_KEY = 'limit';

    const AMOUNT_KEY = 'amount';

    const CURRENCY_KEY = 'currency';

    const CURRENCY_BRL = 'brl';

    const PAYMENT_METHOD_TYPES_KEY = 'payment_method_types';

    const PAYMENT_METHOD_DATA_KEY = 'payment_method_data';

    const PAYMENT_TYPE_BOLETO = 'boleto';

    const PAYMENT_TYPE_PIX = 'pix';

    const PAYMENT_TYPE_CARD = 'card';

    const TAX_ID_KEY = 'tax_id';

    const BILLING_DETAILS_KEY = 'billing_details';

    const ADDRESS_KEY = 'address';

    const LINE1_KEY = 'line1';

    const CITY_KEY = 'city';

    const STATE_KEY = 'state';

    const POSTAL_CODE_KEY = 'postal_code';

    const COUNTRY_KEY = 'country';

    const COUNTRY_BR = 'BR';

    const CONFIRM_KEY = 'confirm';

    const INVOICE_ID_KEY = 'invoice_id';

    const IS_DEFAULT_KEY = 'is_default';

    const INVOICE_SETTINGS_KEY = 'invoice_settings';

    const ITEMS_KEY = 'items';

    const PRICE_KEY = 'price';

    const QUANTITY_KEY = 'quantity';

    const DETAILS_KEY = 'details';

    const INVOICE_PDF_KEY = 'invoice_pdf';

    const HOSTED_INVOICE_URL_KEY = 'hosted_invoice_url';

    public function __construct()
    {
        $secretKey = config('cashier.secret') ?? env('STRIPE_SECRET');

        if (! $secretKey) {
            throw new \Exception('Stripe secret key not configured. Set STRIPE_SECRET in .env file.');
        }

        $this->stripe = new StripeClient($secretKey);
    }

    /**
     * Get subscription details from Stripe
     */
    public function getSubscriptionDetails(string $subscriptionId): ?array
    {
        try {
            $subscription = $this->stripe->subscriptions->retrieve($subscriptionId);

            return [
                self::ID_KEY => $subscription->id,
                self::STATUS_KEY => $subscription->status,
                self::CURRENT_PERIOD_END_KEY => $subscription->current_period_end,
                self::CURRENT_PERIOD_START_KEY => $subscription->current_period_start,
                self::TRIAL_END_KEY => $subscription->trial_end,
                self::DEFAULT_PAYMENT_METHOD_KEY => $subscription->default_payment_method,
                self::CUSTOMER_KEY => $subscription->customer,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get payment method details from Stripe
     */
    public function getPaymentMethod(string $paymentMethodId): ?array
    {
        try {
            $paymentMethod = $this->stripe->paymentMethods->retrieve($paymentMethodId);

            return [
                self::TYPE_KEY => $paymentMethod->type,
                self::BRAND_KEY => $paymentMethod->card->brand ?? null,
                self::LAST4_KEY => $paymentMethod->card->last4 ?? null,
                self::EXP_MONTH_KEY => $paymentMethod->card->exp_month ?? null,
                self::EXP_YEAR_KEY => $paymentMethod->card->exp_year ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create a new subscription in Stripe
     */
    public function createSubscription(string $customerId, string $priceId, array $options = []): ?array
    {
        try {
            // Extrair quantity das options se existir
            $quantity = $options['quantity'] ?? null;
            unset($options['quantity']);

            // Construir item com price e quantity (se fornecido)
            $item = [self::PRICE_KEY => $priceId];
            if ($quantity !== null) {
                $item[self::QUANTITY_KEY] = $quantity;
            }

            $params = array_merge([
                self::CUSTOMER_KEY => $customerId,
                self::ITEMS_KEY => [$item],
            ], $options);

            $subscription = $this->stripe->subscriptions->create($params);

            return [
                self::ID_KEY => $subscription->id,
                self::STATUS_KEY => $subscription->status,
                self::CURRENT_PERIOD_END_KEY => $subscription->current_period_end,
                self::TRIAL_END_KEY => $subscription->trial_end,
                self::ITEMS_KEY => $subscription->items->toArray(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Cancel a subscription in Stripe
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        try {
            $this->stripe->subscriptions->cancel($subscriptionId);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create a new customer in Stripe
     */
    public function createCustomer(array $customerData): ?array
    {
        try {
            $customer = $this->stripe->customers->create($customerData);

            return [
                self::ID_KEY => $customer->id,
                self::NAME_KEY => $customer->name,
                self::EMAIL_KEY => $customer->email,
                self::PHONE_KEY => $customer->phone,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Attach a payment method to a customer
     */
    public function attachPaymentMethod(string $paymentMethodId, string $customerId): bool
    {
        try {
            $this->stripe->paymentMethods->attach($paymentMethodId, [
                self::CUSTOMER_KEY => $customerId,
            ]);

            return true;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            if (str_contains($e->getMessage(), 'already been attached')) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set default payment method for a customer
     */
    public function setDefaultPaymentMethod(string $customerId, string $paymentMethodId): bool
    {
        try {
            $this->stripe->customers->update($customerId, [
                self::INVOICE_SETTINGS_KEY => [
                    self::DEFAULT_PAYMENT_METHOD_KEY => $paymentMethodId,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listPaymentMethods(string $customerId): array
    {
        try {
            $paymentMethods = $this->stripe->paymentMethods->all([
                self::CUSTOMER_KEY => $customerId,
                self::TYPE_KEY => self::PAYMENT_TYPE_CARD,
            ]);

            $customer = $this->getCustomer($customerId);
            $defaultPaymentMethodId = $customer[self::DEFAULT_PAYMENT_METHOD_KEY] ?? null;

            return array_map(function ($pm) use ($defaultPaymentMethodId) {
                return [
                    self::ID_KEY => $pm->id,
                    self::TYPE_KEY => $pm->type,
                    self::BRAND_KEY => $pm->card->brand ?? null,
                    self::LAST4_KEY => $pm->card->last4 ?? null,
                    self::EXP_MONTH_KEY => $pm->card->exp_month ?? null,
                    self::EXP_YEAR_KEY => $pm->card->exp_year ?? null,
                    self::IS_DEFAULT_KEY => $pm->id === $defaultPaymentMethodId,
                ];
            }, $paymentMethods->data);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function detachPaymentMethod(string $paymentMethodId): bool
    {
        try {
            $this->stripe->paymentMethods->detach($paymentMethodId);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCustomer(string $customerId): ?array
    {
        try {
            $customer = $this->stripe->customers->retrieve($customerId);

            return [
                self::ID_KEY => $customer->id,
                self::NAME_KEY => $customer->name,
                self::EMAIL_KEY => $customer->email,
                self::PHONE_KEY => $customer->phone,
                self::DEFAULT_PAYMENT_METHOD_KEY => $customer->invoice_settings->default_payment_method ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * List all invoices for a customer
     */
    public function listInvoices(string $customerId, int $limit = 20): Collection
    {
        try {
            $invoices = $this->stripe->invoices->all([
                self::CUSTOMER_KEY => $customerId,
                self::LIMIT_KEY => $limit,
            ]);

            return collect($invoices->data)->map(fn ($invoice) => InvoiceData::fromResponse(
                $this->mapInvoiceToArray($invoice)
            ));
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get a specific invoice by ID
     */
    public function getInvoice(string $invoiceId): ?InvoiceData
    {
        try {
            $invoice = $this->stripe->invoices->retrieve($invoiceId);

            return InvoiceData::fromResponse($this->mapInvoiceToArray($invoice));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Map Stripe invoice object to array for DTO
     */
    private function mapInvoiceToArray(object $invoice): array
    {
        $paymentMethodInfo = $this->getPaymentMethodInfoFromInvoice($invoice);

        return [
            InvoiceData::ID_KEY => $invoice->id,
            InvoiceData::NUMBER_KEY => $invoice->number ?? InvoiceMessages::INVOICE_NUMBER_PREFIX . date(InvoiceMessages::DATE_FORMAT_YEAR_MONTH, $invoice->created),
            InvoiceData::AMOUNT_KEY => $invoice->amount_due,
            InvoiceData::STATUS_KEY => InvoiceStatus::fromStripeStatus($invoice->status),
            InvoiceData::DUE_DATE_KEY => $invoice->due_date
                ? date(InvoiceMessages::DATE_FORMAT_BR, $invoice->due_date)
                : null,
            InvoiceData::PAID_AT_KEY => $invoice->status === InvoiceStatus::STRIPE_PAID && $invoice->status_transitions?->paid_at
                ? date(InvoiceMessages::DATE_FORMAT_BR, $invoice->status_transitions->paid_at)
                : null,
            InvoiceData::PAYMENT_METHOD_KEY => $paymentMethodInfo[self::TYPE_KEY] ?? null,
            InvoiceData::PAYMENT_METHOD_DETAILS_KEY => $paymentMethodInfo[self::DETAILS_KEY] ?? null,
            InvoiceData::INVOICE_PDF_KEY => $invoice->invoice_pdf ?? null,
            InvoiceData::HOSTED_INVOICE_URL_KEY => $invoice->hosted_invoice_url ?? null,
        ];
    }

    /**
     * Get payment method info from invoice
     *
     * @return array{type: string|null, details: string|null}
     */
    private function getPaymentMethodInfoFromInvoice(object $invoice): array
    {
        if (! $invoice->payment_intent || ! $invoice->default_payment_method) {
            return [self::TYPE_KEY => null, self::DETAILS_KEY => null];
        }

        $paymentMethod = $this->getPaymentMethod($invoice->default_payment_method);

        if (! $paymentMethod) {
            return [self::TYPE_KEY => null, self::DETAILS_KEY => null];
        }

        return [
            self::TYPE_KEY => $paymentMethod[self::TYPE_KEY],
            self::DETAILS_KEY => $this->formatPaymentMethodDetails($paymentMethod),
        ];
    }

    /**
     * Format payment method details for display
     */
    private function formatPaymentMethodDetails(array $paymentMethod): ?string
    {
        $type = $paymentMethod[self::TYPE_KEY] ?? null;

        if ($type === PaymentMethodType::CARD && isset($paymentMethod[self::BRAND_KEY], $paymentMethod[self::LAST4_KEY])) {
            return InvoiceData::formatCardDetails($paymentMethod[self::BRAND_KEY], $paymentMethod[self::LAST4_KEY]);
        }

        if ($type === PaymentMethodType::BOLETO) {
            return InvoiceData::formatBoletoDetails();
        }

        if ($type === PaymentMethodType::PIX) {
            return InvoiceData::formatPixDetails();
        }

        return null;
    }

    /**
     * Create a PaymentIntent for Boleto payment
     */
    public function createBoletoPaymentIntent(array $params): ?BoletoPaymentData
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                self::AMOUNT_KEY => $params[self::AMOUNT_KEY],
                self::CURRENCY_KEY => self::CURRENCY_BRL,
                self::CUSTOMER_KEY => $params[self::CUSTOMER_KEY],
                self::PAYMENT_METHOD_TYPES_KEY => [self::PAYMENT_TYPE_BOLETO],
                self::PAYMENT_METHOD_DATA_KEY => [
                    self::TYPE_KEY => self::PAYMENT_TYPE_BOLETO,
                    self::PAYMENT_TYPE_BOLETO => [
                        self::TAX_ID_KEY => $params[self::TAX_ID_KEY],
                    ],
                    self::BILLING_DETAILS_KEY => [
                        self::NAME_KEY => $params[self::NAME_KEY],
                        self::EMAIL_KEY => $params[self::EMAIL_KEY],
                        self::ADDRESS_KEY => [
                            self::LINE1_KEY => $params[self::ADDRESS_KEY][self::LINE1_KEY],
                            self::CITY_KEY => $params[self::ADDRESS_KEY][self::CITY_KEY],
                            self::STATE_KEY => $params[self::ADDRESS_KEY][self::STATE_KEY],
                            self::POSTAL_CODE_KEY => $params[self::ADDRESS_KEY][self::POSTAL_CODE_KEY],
                            self::COUNTRY_KEY => self::COUNTRY_BR,
                        ],
                    ],
                ],
                self::CONFIRM_KEY => true,
                self::METADATA_KEY => [
                    self::INVOICE_ID_KEY => $params[self::INVOICE_ID_KEY],
                ],
            ]);

            $boletoDetails = $paymentIntent->next_action->boleto_display_details;

            return BoletoPaymentData::fromResponse([
                BoletoPaymentData::BARCODE_KEY => $boletoDetails->number,
                BoletoPaymentData::PDF_URL_KEY => $boletoDetails->hosted_voucher_url,
                BoletoPaymentData::EXPIRES_AT_KEY => date(InvoiceMessages::DATE_FORMAT_BR, $boletoDetails->expires_at),
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create a PaymentIntent for PIX payment
     */
    public function createPixPaymentIntent(array $params): ?PixPaymentData
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                self::AMOUNT_KEY => $params[self::AMOUNT_KEY],
                self::CURRENCY_KEY => self::CURRENCY_BRL,
                self::CUSTOMER_KEY => $params[self::CUSTOMER_KEY],
                self::PAYMENT_METHOD_TYPES_KEY => [self::PAYMENT_TYPE_PIX],
                self::PAYMENT_METHOD_DATA_KEY => [
                    self::TYPE_KEY => self::PAYMENT_TYPE_PIX,
                ],
                self::CONFIRM_KEY => true,
                self::METADATA_KEY => [
                    self::INVOICE_ID_KEY => $params[self::INVOICE_ID_KEY],
                ],
            ]);

            $pixDetails = $paymentIntent->next_action->pix_display_qr_code;

            return PixPaymentData::fromResponse([
                PixPaymentData::QR_CODE_KEY => $pixDetails->data,
                PixPaymentData::QR_CODE_URL_KEY => $pixDetails->image_url_png,
                PixPaymentData::EXPIRES_AT_KEY => date(InvoiceMessages::DATETIME_FORMAT_BR, $pixDetails->expires_at),
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
