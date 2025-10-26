<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use Domain\Financial\AccountsAndCards\Accounts\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class ExistsFileByReferenceDateAction
{
    private AccountFileRepositoryInterface $accountFileRepository;

    public function __construct(AccountFileRepositoryInterface $accountFileRepository)
    {
        $this->accountFileRepository = $accountFileRepository;
    }


    /**
     * @param int $accountId
     * @param string $referenceDate
     * @return bool
     */
    public function execute(int $accountId, string $referenceDate): bool
    {
        return $this->accountFileRepository->existFileByReferenceDate($accountId, $referenceDate);
    }
}
