<?php

namespace Domain\Financial\AccountsAndCards\Cards\Actions;

use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

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
     */
    public function execute(): Collection
    {
        return $this->cardRepository->getCards();
    }
}
