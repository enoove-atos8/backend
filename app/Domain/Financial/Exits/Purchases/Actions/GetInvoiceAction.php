<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;

class GetInvoiceAction
{
    private CardInvoiceRepositoryInterface $cardInvoiceRepository;


    public function __construct(CardInvoiceRepositoryInterface $cardInvoiceRepository)
    {
        $this->cardInvoiceRepository = $cardInvoiceRepository;
    }


    /**
     * @param int $cardId
     * @param string $date
     * @return CardInvoiceData|null
     */
    public function execute(int $cardId, string $date): ?CardInvoiceData
    {
        $invoice = $this->cardInvoiceRepository->getInvoiceByCardIdAndDate($cardId, $date);

        if(!is_null($invoice))
            return $invoice;

        else
            return null;

    }
}
