<?php

namespace App\Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\Interfaces\CardInstallmentsRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardPurchaseRepositoryInterface;
use Domain\Financial\Exits\Purchases\Constants\ReturnMessages;
use Illuminate\Support\Facades\DB;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class DeletePurchaseAction
{
    private CardPurchaseRepositoryInterface $purchaseRepository;

    private CardInstallmentsRepositoryInterface $installmentRepository;

    private CardInvoiceRepositoryInterface $invoiceRepository;

    public function __construct(
        CardPurchaseRepositoryInterface $purchaseRepository,
        CardInstallmentsRepositoryInterface $installmentRepository,
        CardInvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->purchaseRepository = $purchaseRepository;
        $this->installmentRepository = $installmentRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function execute(int $purchaseId): bool
    {
        try {
            DB::beginTransaction();

            // 1. Buscar todas as parcelas da compra
            $installments = $this->installmentRepository->getInstallmentsByPurchaseId($purchaseId);

            if (! $installments || $installments->isEmpty()) {
                throw new GeneralExceptions(ReturnMessages::PURCHASE_NOT_FOUND, 404);
            }

            // 2. Validar se existem parcelas pagas
            foreach ($installments as $installment) {
                if ($installment->status === 'paid') {
                    throw new GeneralExceptions(ReturnMessages::PURCHASE_HAS_PAID_INSTALLMENTS, 422);
                }
            }

            // 3. Para cada parcela, creditar o valor de volta na fatura
            foreach ($installments as $installment) {
                if ($installment->cardInvoiceData && $installment->cardInvoiceData->id) {
                    $invoiceId = $installment->cardInvoiceData->id;

                    // Buscar a fatura atual
                    $invoice = $this->invoiceRepository->getInvoiceById($invoiceId);

                    if ($invoice) {
                        // Subtrair o valor da parcela do total da fatura
                        $newAmount = $invoice->amount - $installment->installmentAmount;

                        // Garantir que o valor não fique negativo
                        $newAmount = max(0, $newAmount);

                        // Atualizar o valor da fatura
                        $this->invoiceRepository->updateInvoiceAmount($invoiceId, $newAmount);
                    }
                }
            }

            // 4. Excluir todas as parcelas (soft delete)
            $installmentsDeleted = $this->installmentRepository->deleteInstallmentsByPurchaseId($purchaseId);

            if (! $installmentsDeleted) {
                throw new GeneralExceptions(ReturnMessages::PURCHASE_DELETE_ERROR, 500);
            }

            // 5. Excluir a compra (soft delete)
            $purchaseDeleted = $this->purchaseRepository->deletePurchase($purchaseId);

            if (! $purchaseDeleted) {
                throw new GeneralExceptions(ReturnMessages::PURCHASE_DELETE_ERROR, 500);
            }

            DB::commit();

            return true;

        } catch (Throwable $e) {
            DB::rollBack();
            throw new GeneralExceptions(
                $e->getMessage() ?: ReturnMessages::PURCHASE_DELETE_ERROR,
                $e->getCode() ?: 500
            );
        }
    }
}
