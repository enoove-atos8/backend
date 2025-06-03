<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\Interfaces\CardInstallmentsRepositoryInterface;
use Illuminate\Support\Collection;

class GetInstallmentsAction
{
    private CardInstallmentsRepositoryInterface $cardInstallmentsRepository;


    public function __construct(CardInstallmentsRepositoryInterface $cardInstallmentsRepository)
    {
        $this->cardInstallmentsRepository = $cardInstallmentsRepository;
    }


    /**
     * @param int $cardId
     * @param string $date
     * @return Collection|null
     */
    public function execute(int $cardId, string $date): ?Collection
    {
        $installments = $this->cardInstallmentsRepository->getInstallmentsWithPurchase($cardId, $date);

        if(!is_null($installments))
            return $installments;

        else
            return null;

    }
}
