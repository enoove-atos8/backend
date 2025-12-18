<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators;

class MonthSummaryData
{
    public function __construct(
        public int $accountId,
        public string $bankName,
        public string $accountType,
        public ?float $totalCredits,
        public ?float $totalDebits,
        public ?float $balance,
        public ?int $creditCount,
        public ?int $debitCount,
        public ?float $previousMonthBalance,
        public ?float $currentMonthBalance,
        public ?string $referenceDate,
        public ?string $fileProcessStatus
    ) {}

    public static function fromResponse(array $data): self
    {
        $rawStatus = $data['file_process_status'] ?? null;
        $hasProcessedFile = $rawStatus === 'movements_done';
        $fileProcessStatus = $hasProcessedFile ? 'movements_done' : null;

        $totalCredits = $hasProcessedFile ? (float) ($data['total_credits'] ?? 0) : null;
        $totalDebits = $hasProcessedFile ? (float) ($data['total_debits'] ?? 0) : null;
        $balance = $hasProcessedFile ? $totalCredits - $totalDebits : null;

        return new self(
            accountId: (int) $data['account_id'],
            bankName: $data['bank_name'],
            accountType: $data['account_type'],
            totalCredits: $totalCredits,
            totalDebits: $totalDebits,
            balance: $balance,
            creditCount: $hasProcessedFile ? (int) ($data['credit_count'] ?? 0) : null,
            debitCount: $hasProcessedFile ? (int) ($data['debit_count'] ?? 0) : null,
            previousMonthBalance: $hasProcessedFile && isset($data['previous_month_balance']) ? (float) $data['previous_month_balance'] : null,
            currentMonthBalance: $hasProcessedFile && isset($data['current_month_balance']) ? (float) $data['current_month_balance'] : null,
            referenceDate: $hasProcessedFile ? $data['reference_date'] : null,
            fileProcessStatus: $fileProcessStatus
        );
    }
}
