<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use Domain\Financial\AccountsAndCards\Accounts\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class SaveAccountFileAction
{
    private AccountFileRepositoryInterface $accountFileRepository;

    public function __construct(AccountFileRepositoryInterface $accountFileRepository)
    {
        $this->accountFileRepository = $accountFileRepository;
    }


    /**
     * @param AccountFileData $accountFileData
     * @return AccountFileData
     * @throws GeneralExceptions
     */
    public function execute(AccountFileData $accountFileData): AccountFileData
    {
        $file = $this->accountFileRepository->saveFile($accountFileData);

        if(!is_null($file->id))
            return $file;

        else
            throw new GeneralExceptions(ReturnMessages::FILE_NOT_CREATED, 500);
    }
}
