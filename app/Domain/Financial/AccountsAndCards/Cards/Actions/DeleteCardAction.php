<?php

namespace Domain\Financial\AccountsAndCards\Cards\Actions;

use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteCardAction
{
    public function __construct(
        private CardRepositoryInterface $cardRepository
    ) {}

    /**
     * Execute the action to permanently delete a card.
     *
     * @param int $cardId
     * @return bool
     * @throws GeneralExceptions
     */
    public function execute(int $cardId): bool
    {
        $deleted = $this->cardRepository->deleteCard($cardId);

        if ($deleted) {
            return true;
        }

        throw new GeneralExceptions(ReturnMessages::DELETED_CARD_ERROR, 500);
    }
}
