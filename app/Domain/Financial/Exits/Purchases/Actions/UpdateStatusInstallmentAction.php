<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\Interfaces\CardInstallmentsRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateStatusInstallmentAction
{
    private CardInstallmentsRepositoryInterface $cardInstallmentsRepository;


    public function __construct(
        CardInstallmentsRepositoryInterface $cardInstallmentsRepository,
    )
    {
        $this->cardInstallmentsRepository = $cardInstallmentsRepository;
    }


    /**
     * @param int $invoiceId
     * @param string $date
     * @param string $status
     * @return void
     */
    public function execute(int $invoiceId, string $date, string $status): void
    {
        $this->cardInstallmentsRepository->updateStatusInstallment($invoiceId, $date, $status);
    }
}
