<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInstallmentsRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardPurchaseRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardByIdAction;
use Domain\Financial\Exits\Purchases\Constants\ReturnMessages;
use Illuminate\Support\Carbon;
use Infrastructure\Exceptions\GeneralExceptions;

class PostponePurchaseAction
{
    private const MAX_MONTHS_DIFFERENCE = 1;

    private CardInstallmentsRepositoryInterface $cardInstallmentsRepository;

    private CardPurchaseRepositoryInterface $cardPurchaseRepository;

    private CardInvoiceRepositoryInterface $cardInvoiceRepository;

    private GetCardByIdAction $getCardByIdAction;

    private CreateInvoiceAction $createInvoiceAction;

    private UpdateInvoiceAmountAction $updateInvoiceAmountAction;

    public function __construct(
        CardInstallmentsRepositoryInterface $cardInstallmentsRepository,
        CardPurchaseRepositoryInterface $cardPurchaseRepository,
        CardInvoiceRepositoryInterface $cardInvoiceRepository,
        GetCardByIdAction $getCardByIdAction,
        CreateInvoiceAction $createInvoiceAction,
        UpdateInvoiceAmountAction $updateInvoiceAmountAction
    ) {
        $this->cardInstallmentsRepository = $cardInstallmentsRepository;
        $this->cardPurchaseRepository = $cardPurchaseRepository;
        $this->cardInvoiceRepository = $cardInvoiceRepository;
        $this->getCardByIdAction = $getCardByIdAction;
        $this->createInvoiceAction = $createInvoiceAction;
        $this->updateInvoiceAmountAction = $updateInvoiceAmountAction;
    }

    /**
     * @throws GeneralExceptions
     */
    public function execute(int $purchaseId): bool
    {
        $purchase = $this->cardPurchaseRepository->getPurchaseById($purchaseId);

        if (is_null($purchase)) {
            throw new GeneralExceptions(ReturnMessages::PURCHASE_NOT_FOUND, 404);
        }

        if ($this->cardInstallmentsRepository->hasPaidInstallments($purchaseId)) {
            throw new GeneralExceptions(ReturnMessages::PURCHASE_HAS_PAID_INSTALLMENTS, 400);
        }

        if ($this->wasAlreadyPostponed($purchaseId, $purchase->date)) {
            throw new GeneralExceptions(ReturnMessages::PURCHASE_ALREADY_POSTPONED, 400);
        }

        $card = $this->getCardByIdAction->execute($purchase->cardId);
        $installments = $this->cardInstallmentsRepository->getInstallmentsByPurchaseId($purchaseId);

        if (is_null($installments) || $installments->isEmpty()) {
            throw new GeneralExceptions(ReturnMessages::INSTALLMENT_NOT_FOUND, 404);
        }

        foreach ($installments as $installment) {
            $currentDate = Carbon::parse($installment->date);
            $newDate = $currentDate->copy()->addMonthNoOverflow();
            $newReferenceDate = $newDate->format('Y-m');

            $oldInvoice = $this->cardInvoiceRepository->getInvoiceById($installment->cardInvoiceData->id);

            $newInvoice = $this->cardInvoiceRepository->getInvoiceByCardIdAndDate($purchase->cardId, $newReferenceDate);

            if (is_null($newInvoice)) {
                $invoiceData = new CardInvoiceData([
                    'cardId' => $purchase->cardId,
                    'status' => 'open',
                    'amount' => 0,
                    'referenceDate' => $newReferenceDate,
                    'isClosed' => false,
                    'deleted' => false,
                ]);
                $newInvoice = $this->createInvoiceAction->execute($invoiceData, $card->closingDay, $newReferenceDate);
            }

            $this->cardInstallmentsRepository->updateInstallmentDateAndInvoice(
                $installment->id,
                $newDate->format('Y-m'),
                $newInvoice->id
            );

            $this->updateInvoiceAmountAction->execute($newInvoice->id, $installment->installmentAmount);

            if ($oldInvoice) {
                $this->updateInvoiceAmountAction->execute($oldInvoice->id, -$installment->installmentAmount);
            }
        }

        return true;
    }

    private function wasAlreadyPostponed(int $purchaseId, ?string $purchaseDate): bool
    {
        $firstInstallmentDate = $this->cardInstallmentsRepository->getFirstInstallmentDate($purchaseId);

        if (is_null($purchaseDate) || is_null($firstInstallmentDate)) {
            return false;
        }

        $monthsDifference = $this->calculateMonthsDifference($purchaseDate, $firstInstallmentDate);

        return $monthsDifference > self::MAX_MONTHS_DIFFERENCE;
    }

    private function calculateMonthsDifference(string $purchaseDate, string $firstInstallmentDate): int
    {
        $purchaseDateCarbon = Carbon::parse($purchaseDate);
        $firstInstallmentCarbon = Carbon::parse($firstInstallmentDate);

        $purchaseYearMonth = ($purchaseDateCarbon->year * 12) + $purchaseDateCarbon->month;
        $installmentYearMonth = ($firstInstallmentCarbon->year * 12) + $firstInstallmentCarbon->month;

        return $installmentYearMonth - $purchaseYearMonth;
    }
}
