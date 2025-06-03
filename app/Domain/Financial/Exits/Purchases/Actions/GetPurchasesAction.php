<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardPurchaseData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardPurchaseRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetPurchasesAction
{
    private CardPurchaseRepositoryInterface $cardPurchaseRepository;


    public function __construct(CardPurchaseRepositoryInterface $cardPurchaseRepository)
    {
        $this->cardPurchaseRepository = $cardPurchaseRepository;
    }


    /**
     * @param int $cardId
     * @param string $date
     * @return CardPurchaseData|null
     * @throws GeneralExceptions
     */
    public function execute(int $cardId, string $date): ?Collection
    {
        $purchases = $this->cardPurchaseRepository->getPurchases($cardId, $date);

        if(count($purchases) > 0)
            return $purchases;

        else
            throw new GeneralExceptions(ReturnMessages::PURCHASES_NOT_FOUND, 404);

    }
}
