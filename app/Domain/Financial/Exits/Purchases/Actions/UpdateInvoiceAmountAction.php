<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Exception;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateInvoiceAmountAction
{
    private CardInvoiceRepositoryInterface $cardInvoiceRepository;
    private GetInvoiceByIdAction $getInvoiceByIdAction;


    public function __construct(
        CardInvoiceRepositoryInterface $cardInvoiceRepository,
        GetInvoiceByIdAction $getInvoiceByIdAction,
    )
    {
        $this->cardInvoiceRepository = $cardInvoiceRepository;
        $this->getInvoiceByIdAction = $getInvoiceByIdAction;
    }


    /**
     * @param int $invoiceId
     * @param float $amount
     * @return void
     * @throws GeneralExceptions
     */
    public function execute(int $invoiceId, float $amount): void
    {

        $invoice = $this->getInvoiceByIdAction->execute($invoiceId);
        $newAmount = (float) $invoice->amount + $amount;

        if(!is_null($invoice->id))
            $this->cardInvoiceRepository->updateInvoiceAmount($invoiceId, $newAmount);

        else
            throw new GeneralExceptions(ReturnMessages::INVOICE_NOT_FOUND, 404);
    }
}
