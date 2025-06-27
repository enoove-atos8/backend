<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\Interfaces\CardInstallmentsRepositoryInterface;
use Illuminate\Support\Collection;

class GetInstallmentsByPurchaseAction
{
    private CardInstallmentsRepositoryInterface $cardInstallmentsRepository;


    public function __construct(CardInstallmentsRepositoryInterface $cardInstallmentsRepository)
    {
        $this->cardInstallmentsRepository = $cardInstallmentsRepository;
    }


    /**
     * @param int $purchaseId
     * @return Collection|null
     */
    public function execute(int $purchaseId): ?Collection
    {
        $installments = $this->cardInstallmentsRepository->getInstallmentsByPurchaseId($purchaseId);

        if(!is_null($installments))
            return $installments;

        else
            return null;

    }
}
