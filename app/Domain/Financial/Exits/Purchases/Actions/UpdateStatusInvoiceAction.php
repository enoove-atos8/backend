<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateStatusInvoiceAction
{
    private CardInvoiceRepositoryInterface $cardInvoiceRepository;


    public function __construct(
        CardInvoiceRepositoryInterface $cardInvoiceRepository,
    )
    {
        $this->cardInvoiceRepository = $cardInvoiceRepository;
    }


    /**
     * @param int $invoiceId
     * @param string $status
     * @return void
     */
    public function execute(int $invoiceId, string $status): void
    {
        $this->cardInvoiceRepository->updateInvoiceStatus($invoiceId, $status);
    }
}
