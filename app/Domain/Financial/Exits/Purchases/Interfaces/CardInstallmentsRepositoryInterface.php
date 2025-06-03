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
     * @param int $cardId
     * @param string $date
     * @return CardInvoiceData|null
     */
    public function getInstallmentsWithPurchase(int $cardId, string $date): ?Collection;


    /**
     * Returns the card invoice created
     *
     * @param CardInstallmentData $cardInstallmentData
     * @return CardInstallmentData
     */
    public function createInstallment(CardInstallmentData $cardInstallmentData): CardInstallmentData;
}
