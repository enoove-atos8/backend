<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use Domain\Financial\AccountsAndCards\Accounts\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetAccountFilesAction
{
    private AccountFileRepositoryInterface $accountFileRepository;

    public function __construct(AccountFileRepositoryInterface $accountFileRepository)
    {
        $this->accountFileRepository = $accountFileRepository;
    }


    /**
     * @param int $accountId
     * @return Collection
     * @throws GeneralExceptions
     */
    public function execute(int $accountId): Collection
    {
        $files = $this->accountFileRepository->getFiles($accountId);

        if(count($files) > 0)
            return $files;

        else
            throw new GeneralExceptions(ReturnMessages::FILES_NOT_FOUND, 404);
    }
}
