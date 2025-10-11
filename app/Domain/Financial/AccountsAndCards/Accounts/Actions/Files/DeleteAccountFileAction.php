<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use Domain\Financial\AccountsAndCards\Accounts\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteAccountFileAction
{
    private AccountFileRepositoryInterface $accountFileRepository;

    public function __construct(AccountFileRepositoryInterface $accountFileRepository)
    {
        $this->accountFileRepository = $accountFileRepository;
    }


    /**
     * @param int $accountId
     * @param int $id
     * @return AccountFileData
     */
    public function execute(int $accountId, int $id): mixed
    {
        return $this->accountFileRepository->deleteFile($accountId, $id);
    }
}
