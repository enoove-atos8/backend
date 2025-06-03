<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;

class GetInvoicesByCardIdAction
{
    private CardInvoiceRepositoryInterface $cardInvoiceRepository;


    public function __construct(CardInvoiceRepositoryInterface $cardInvoiceRepository)
    {
        $this->cardInvoiceRepository = $cardInvoiceRepository;
    }


    /**
     * @param int $cardId
     * @return CardInvoiceData|null
     */
    public function execute(int $cardId): ?CardInvoiceData
    {
        $invoice = $this->cardInvoiceRepository->getInvoicesByCardId($cardId);

        if(!is_null($invoice))
            return $invoice;

        else
            return null;

    }
}
