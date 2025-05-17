<?php

namespace Domain\Financial\AccountsAndCards\Cards\Actions;

use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Cards\DataTransferObjects\CardData;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Models\Card;
use Exception;
use Infrastructure\Exceptions\GeneralExceptions;

class SaveCardAction
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
     * @param CardData $cardData
     * @return Card The ID of the saved card
     * @throws GeneralExceptions
     */
    public function execute(CardData $cardData): Card
    {

        $card = $this->cardRepository->saveCard($cardData);

        if(!is_null($card->id))
            return $card;

        else
            throw new GeneralExceptions(ReturnMessages::CARDS_NOT_CREATED, 500);

    }
}
