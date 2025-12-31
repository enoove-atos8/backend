<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardPurchaseData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInstallmentsRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardPurchaseRepositoryInterface;
use Illuminate\Support\Carbon;

class GetInstallmentsByPurchaseAction
{
    private const MAX_MONTHS_DIFFERENCE = 1;

    private CardInstallmentsRepositoryInterface $cardInstallmentsRepository;

    private CardPurchaseRepositoryInterface $cardPurchaseRepository;

    public function __construct(
        CardInstallmentsRepositoryInterface $cardInstallmentsRepository,
        CardPurchaseRepositoryInterface $cardPurchaseRepository
    ) {
        $this->cardInstallmentsRepository = $cardInstallmentsRepository;
        $this->cardPurchaseRepository = $cardPurchaseRepository;
    }

    /**
     * @return array{purchase: CardPurchaseData|null, installments: \Illuminate\Support\Collection|null}
     */
    public function execute(int $purchaseId): array
    {
        $installments = $this->cardInstallmentsRepository->getInstallmentsByPurchaseId($purchaseId);
        $purchase = $this->cardPurchaseRepository->getPurchaseById($purchaseId);

        if ($purchase) {
            $purchase->canPostpone = $this->canPostpone($purchaseId, $purchase->date);
        }

        return [
            'purchase' => $purchase,
            'installments' => $installments,
        ];
    }

    private function canPostpone(int $purchaseId, ?string $purchaseDate): bool
    {
        if ($this->cardInstallmentsRepository->hasPaidInstallments($purchaseId)) {
            return false;
        }

        $firstInstallmentDate = $this->cardInstallmentsRepository->getFirstInstallmentDate($purchaseId);

        if (is_null($purchaseDate) || is_null($firstInstallmentDate)) {
            return false;
        }

        $monthsDifference = $this->calculateMonthsDifference($purchaseDate, $firstInstallmentDate);

        return $monthsDifference <= self::MAX_MONTHS_DIFFERENCE;
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
