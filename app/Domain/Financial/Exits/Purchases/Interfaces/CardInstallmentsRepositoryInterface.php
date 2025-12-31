<?php

namespace App\Domain\Financial\Exits\Purchases\Interfaces;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInstallmentData;
use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use Illuminate\Support\Collection;

interface CardInstallmentsRepositoryInterface
{
    /**
     * Returns the card installments by card id and date reference
     *
     * @return CardInvoiceData|null
     */
    public function getInstallmentsWithPurchase(int $cardId, string $date): ?Collection;

    /**
     * Returns the card installments by card id and date reference
     *
     * @return CardInvoiceData|null
     */
    public function getInstallmentsByPurchaseId(int $purchaseId): ?Collection;

    /**
     * Update the status of installment
     */
    public function updateStatusInstallment(int $invoiceId, string $date, string $status): void;

    /**
     * Returns the card invoice created
     */
    public function createInstallment(CardInstallmentData $cardInstallmentData): CardInstallmentData;

    /**
     * Delete all installments by purchase id (soft delete)
     */
    public function deleteInstallmentsByPurchaseId(int $purchaseId): bool;

    /**
     * Check if purchase has any paid installments
     */
    public function hasPaidInstallments(int $purchaseId): bool;

    /**
     * Update installment date and invoice
     */
    public function updateInstallmentDateAndInvoice(int $installmentId, string $date, int $invoiceId): bool;

    /**
     * Get first installment date by purchase id
     */
    public function getFirstInstallmentDate(int $purchaseId): ?string;
}
