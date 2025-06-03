<?php

namespace Domain\Financial\AccountsAndCards\Cards\Actions;

use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Cards\DataTransferObjects\CardData;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class GetCardByIdAction
{
    private CardRepositoryInterface $cardRepository;

    public function __construct(CardRepositoryInterface $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    /**
     * Execute the action to retrieve all cards.
     *
     * @param int $id
     * @return CardData|null
     * @throws GeneralExceptions
     */
    public function execute(int $id): ?CardData
    {
        $card = $this->cardRepository->getCardById($id);

        if(!is_null($card->id))
            return $card;

        else
            throw new GeneralExceptions(ReturnMessages::CARDS_NOT_FOUND, 404);
    }
}
