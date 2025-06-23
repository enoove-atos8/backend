<?php

namespace App\Domain\Financial\Exits\Purchases\Interfaces;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardPurchaseData;
use Illuminate\Support\Collection;

interface CardPurchaseRepositoryInterface
{
    /**
     * Returns the purchases of card to a specific invoice
     *
     * @param int $cardId
     * @param string $date
     * @return CardPurchaseData|null
     */
    public function getPurchases(int $cardId, string $date): ?Collection;


    /**
     * Create a new purchase
     *
     * @param CardPurchaseData $cardPurchaseData
     * @return CardPurchaseData
     */
    public function createPurchase(CardPurchaseData $cardPurchaseData): CardPurchaseData;
}
