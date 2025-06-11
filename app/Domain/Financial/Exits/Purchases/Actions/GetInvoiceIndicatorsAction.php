<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardByIdAction;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class GetInvoiceIndicatorsAction
{
    private CardInvoiceRepositoryInterface $cardInvoiceRepository;
    private GetCardByIdAction $getCardByIdAction;


    public function __construct(
        CardInvoiceRepositoryInterface $cardInvoiceRepository,
        GetCardByIdAction $getCardByIdAction
    )
    {
        $this->cardInvoiceRepository = $cardInvoiceRepository;
        $this->getCardByIdAction = $getCardByIdAction;
    }


    /**
     * @param int $cardId
     * @param int $invoiceId
     * @return CardInvoiceData|null
     * @throws GeneralExceptions
     */
    public function execute(int $cardId, int $invoiceId): ?array
    {
        $result = [];
        $card = $this->getCardByIdAction->execute($cardId);
        $invoice = $this->cardInvoiceRepository->getInvoiceById($invoiceId);

        if(!is_null($invoice))
        {
            $totalInvoice = $invoice->amount;
            $result = [
                'total'     => $totalInvoice,
                'dueDate'   =>  $card->dueDay,
                'closingDay'   =>  $card->closingDay,
            ];
        }

        return $result;
    }
}
