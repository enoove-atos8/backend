<?php

namespace Domain\Financial\AccountsAndCards\Cards\Actions;

use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Cards\DataTransferObjects\CardData;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Models\Card;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteCardAction
{
    protected CardRepositoryInterface $cardRepository;

    /**
     * Create a new SaveCardAction instance.
     *
     * @param CardRepositoryInterface $cardRepository
     */
    public function __construct(CardRepositoryInterface $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    /**
     * Execute the action to save a card.
     *
     * @param $cardId
     * @return bool The ID of the saved card
     * @throws GeneralExceptions
     */
    public function execute($cardId): bool
    {

        $card = $this->cardRepository->deleteCard($cardId);

        if($card)
            return true;

        else
            throw new GeneralExceptions(ReturnMessages::DELETED_CARD_ERROR, 500);

    }
}
