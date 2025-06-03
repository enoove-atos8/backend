<?php

namespace App\Domain\Financial\Exits\Purchases\Interfaces;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use Illuminate\Support\Collection;

interface CardInvoiceRepositoryInterface
{
    /**
     * Returns the card invoice data for a given card ID and reference date.
     *
     * @param int $cardId
     * @param string $referenceDate Expected format: 'Y-m-d'
     * @return CardInvoiceData|null
     */
    public function getInvoiceByCardIdAndDate(int $cardId, string $referenceDate): ?CardInvoiceData;


    /**
     * Returns the card invoice data by id
     *
     * @param int $invoiceId
     * @return CardInvoiceData|null
     */
    public function getInvoiceById(int $invoiceId): ?CardInvoiceData;


    /**
     * Returns the card invoice data by card id
     *
     * @param int $cardId
     * @return CardInvoiceData|null
     */
    public function getInvoicesByCardId(int $cardId): ?Collection;


    /**
     * Returns the card invoice created
     *
     * @param CardInvoiceData $cardInvoiceData
     * @return CardInvoiceData
     */
    public function createInvoice(CardInvoiceData $cardInvoiceData): CardInvoiceData;


    /**
     * Returns the card invoice created
     *
     * @param int $invoiceId
     * @param float $amount
     * @return void
     */
    public function updateInvoiceAmount(int $invoiceId, float $amount): void;
}
