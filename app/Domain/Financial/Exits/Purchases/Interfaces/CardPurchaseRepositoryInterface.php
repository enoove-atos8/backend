<?php

namespace App\Domain\Financial\Exits\Purchases\Interfaces;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardPurchaseData;
use Illuminate\Support\Collection;

interface CardPurchaseRepositoryInterface
{
    /**
     * Returns the purchases of card to a specific invoice
     *
     * @return CardPurchaseData|null
     */
    public function getPurchases(int $cardId, string $date): ?Collection;

    /**
     * Returns a purchase by id
     */
    public function getPurchaseById(int $purchaseId): ?CardPurchaseData;

    /**
     * Create a new purchase
     */
    public function createPurchase(CardPurchaseData $cardPurchaseData): CardPurchaseData;

    /**
     * Delete a purchase (soft delete)
     */
    public function deletePurchase(int $purchaseId): bool;
}
