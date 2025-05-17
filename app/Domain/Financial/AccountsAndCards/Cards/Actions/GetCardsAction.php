<?php

namespace Domain\Financial\AccountsAndCards\Cards\Actions;

use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardRepository;

class GetCardsAction
{
    private CardRepositoryInterface $cardRepository;

    public function __construct(CardRepositoryInterface $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    /**
     * Execute the action to retrieve all cards.
     *
     * @return Collection
     * @throws GeneralExceptions
     */
    public function execute(): Collection
    {
        $cards = $this->cardRepository->getCards();

        if(count($cards) > 0)
            return $cards;
        else
            throw new GeneralExceptions(ReturnMessages::CARDS_NOT_FOUND, 404);
    }
}
