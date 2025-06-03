<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;

class GetInvoiceByIdAction
{
    private CardInvoiceRepositoryInterface $cardInvoiceRepository;


    public function __construct(CardInvoiceRepositoryInterface $cardInvoiceRepository)
    {
        $this->cardInvoiceRepository = $cardInvoiceRepository;
    }


    /**
     * @param int $invoiceId
     * @return CardInvoiceData|null
     */
    public function execute(int $invoiceId): ?CardInvoiceData
    {

        $invoice = $this->cardInvoiceRepository->getInvoiceById($invoiceId);

        if(!is_null($invoice->id))
            return $invoice;

        else
            return null;
    }
}
