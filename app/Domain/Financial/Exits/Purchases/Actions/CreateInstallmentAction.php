<?php

namespace Domain\Financial\Exits\Purchases\Actions;


use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInstallmentData;
use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInstallmentsRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardByIdAction;
use Domain\Financial\Exits\Purchases\Constants\ReturnMessages;
use Illuminate\Support\Carbon;
use Infrastructure\Exceptions\GeneralExceptions;
use InvalidArgumentException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CreateInstallmentAction
{
    private CardInstallmentsRepositoryInterface $cardInstallmentsRepository;
    private GetCardByIdAction $getCardByIdAction;
    private GetInvoiceAction $getInvoiceAction;
    private CreateInvoiceAction $createInvoiceAction;
    private UpdateInvoiceAmountAction $updateInvoiceAmountAction;


    public function __construct(
        CardInstallmentsRepositoryInterface $cardInstallmentsRepository,
        GetCardByIdAction $getCardByIdAction,
        GetInvoiceAction $getInvoiceAction,
        CreateInvoiceAction $createInvoiceAction,
        UpdateInvoiceAmountAction $updateInvoiceAmountAction
    )
    {
        $this->cardInstallmentsRepository = $cardInstallmentsRepository;
        $this->getCardByIdAction = $getCardByIdAction;
        $this->getInvoiceAction = $getInvoiceAction;
        $this->createInvoiceAction = $createInvoiceAction;
        $this->updateInvoiceAmountAction = $updateInvoiceAmountAction;
    }


    /**
     * @param CardInstallmentData $cardInstallmentData
     * @param string|null $previousReference
     * @return CardInstallmentData|null
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function execute(CardInstallmentData $cardInstallmentData, ?string $previousReference = null): ?CardInstallmentData
    {
        $card = $this->getCardByIdAction->execute($cardInstallmentData->cardId);

        $purchaseDate = Carbon::parse($cardInstallmentData->date);
        $closingDay = (int) $card->closingDay;

        $referenceDate = $this->calculateReferenceDate($purchaseDate, $closingDay, $cardInstallmentData->isFirstInstallment, $previousReference);

        $invoice = $this->getInvoiceAction->execute($cardInstallmentData->cardId, $referenceDate);

        if (is_null($invoice))
        {
            $invoiceData = CardInvoiceData::fromInstallmentData($cardInstallmentData);
            $invoice = $this->createInvoiceAction->execute($invoiceData, $card->closingDay, $referenceDate);
        }

        $cardInstallmentData->cardInvoiceData->id = (int) $invoice->id;
        $cardInstallmentData->date = $referenceDate;

        $installment = $this->cardInstallmentsRepository->createInstallment($cardInstallmentData);

        if(!is_null($installment->id))
        {
            $this->updateInvoiceAmountAction->execute($invoice->id, $cardInstallmentData->installmentAmount);
            $installment->date = $referenceDate;
            return $installment;
        }

        throw new GeneralExceptions(ReturnMessages::INSTALLMENT_NOT_CREATED, 500);
    }



    /**
     * @param Carbon $purchaseDate
     * @param int $closingDay
     * @param bool $isFirstInstallment
     * @param string|null $previousReference
     * @return string
     */
    public function calculateReferenceDate(Carbon $purchaseDate, int $closingDay, bool $isFirstInstallment, ?string $previousReference = null): string
    {
        $closingDateCurrent = $purchaseDate->copy()->day($closingDay);
        $closingDateNext = $purchaseDate->copy()->addMonthNoOverflow()->day($closingDay);

        if (!$isFirstInstallment)
            return Carbon::createFromFormat('Y-m', $previousReference)->addMonthNoOverflow()->format('Y-m');

        if ($purchaseDate->lessThan($closingDateCurrent))
            return $purchaseDate->format('Y-m');

        if ($purchaseDate->equalTo($closingDateCurrent))
            return $closingDateNext->format('Y-m');

        if ($purchaseDate->lessThan($closingDateNext))
            return $closingDateNext->format('Y-m');

        return $closingDateNext->copy()->addMonthNoOverflow()->format('Y-m');
    }
}
