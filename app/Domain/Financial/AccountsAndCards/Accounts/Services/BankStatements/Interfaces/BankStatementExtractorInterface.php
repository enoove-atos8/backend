<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\Interfaces;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;

interface BankStatementExtractorInterface
{
    /**
     * Extract bank statement data from file
     *
     * @param string $filePath
     * @param AccountFileData $file
     * @return array
     */
    public function extract(string $filePath, AccountFileData $file): array;

    /**
     * Check if this extractor supports the given bank
     *
     * @param string $bankName
     * @return bool
     */
    public function supports(string $bankName): bool;
}
