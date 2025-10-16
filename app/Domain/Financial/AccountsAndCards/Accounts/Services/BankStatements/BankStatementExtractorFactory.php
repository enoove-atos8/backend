<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements;

use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\Extractors\CaixaStatementExtractor;
use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\Interfaces\BankStatementExtractorInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class BankStatementExtractorFactory
{
    /**
     * Create a bank statement extractor based on bank name
     *
     * @param string $bankName
     * @return BankStatementExtractorInterface
     * @throws GeneralExceptions
     */
    public function make(string $bankName): BankStatementExtractorInterface
    {
        $extractor = match(strtolower($bankName)) {
            'caixa','caixa economica', 'caixa economica federal' => new CaixaStatementExtractor(),
            // Add more banks here:
            // 'bradesco' => new BradescoStatementExtractor(),
            // 'itau' => new ItauStatementExtractor(),
            default => throw new GeneralExceptions("Bank statement extractor not found for: {$bankName}", 404)
        };

        return $extractor;
    }
}
