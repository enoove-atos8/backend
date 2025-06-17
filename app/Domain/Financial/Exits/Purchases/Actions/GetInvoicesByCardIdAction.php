<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use Illuminate\Support\Collection;

class GetInvoicesByCardIdAction
{
    private CardInvoiceRepositoryInterface $cardInvoiceRepository;


    public function __construct(CardInvoiceRepositoryInterface $cardInvoiceRepository)
    {
        $this->cardInvoiceRepository = $cardInvoiceRepository;
    }


    /**
     * @param int $cardId
     * @param bool $getClosedInvoices
     * @return CardInvoiceData|null
     */
    public function execute(int $cardId, bool $getClosedInvoices = false): ?Collection
    {
        $invoices = $this->cardInvoiceRepository->getInvoicesByCardId($cardId, $getClosedInvoices);

        if(!is_null($invoices))
            return $invoices;

        else
            return null;

    }
}
