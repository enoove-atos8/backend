<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use Illuminate\Support\Collection;

class GetInvoicesByCardIdAndStatusAction
{
    private CardInvoiceRepositoryInterface $cardInvoiceRepository;


    public function __construct(CardInvoiceRepositoryInterface $cardInvoiceRepository)
    {
        $this->cardInvoiceRepository = $cardInvoiceRepository;
    }


    /**
     * @param int $cardId
     * @param string $status
     * @return CardInvoiceData|null
     */
    public function execute(int $cardId, string $status): ?Collection
    {
        $invoices = $this->cardInvoiceRepository->getInvoicesByCardIdAndStatus($cardId, $status);

        if(!is_null($invoices))
            return $invoices;

        else
            return null;

    }
}
