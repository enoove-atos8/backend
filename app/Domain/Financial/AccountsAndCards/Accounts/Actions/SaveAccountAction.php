<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions;

use Domain\Financial\AccountsAndCards\Accounts\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class SaveAccountAction
{
    protected AccountRepositoryInterface $accountRepository;

    /**
     * Create a new SaveCardAction instance.
     *
     * @param AccountRepositoryInterface $accountRepository
     */
    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Execute the action to save a card.
     *
     * @param AccountData $accountData
     * @return AccountData The ID of the saved card
     * @throws GeneralExceptions
     */
    public function execute(AccountData $accountData): AccountData
    {

        $account = $this->accountRepository->saveAccount($accountData);

        if(!is_null($account->id))
            return $account;

        else
            throw new GeneralExceptions(ReturnMessages::ACCOUNT_NOT_CREATED, 500);

    }
}
