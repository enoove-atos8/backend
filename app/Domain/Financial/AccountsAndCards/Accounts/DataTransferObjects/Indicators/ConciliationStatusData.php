<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators;

class ConciliationStatusData
{
    private const ACCOUNT_TYPE_LABELS = [
        'checking_account' => 'Corrente',
        'savings_account' => 'Poupança',
    ];

    public function __construct(
        public int $accountId,
        public string $accountName,
        public string $referenceMonth,
        public int $creditConciliated,
        public int $creditNotConciliated,
        public int $debitConciliated,
        public int $debitNotConciliated
    ) {}

    public static function fromResponse(array $data): self
    {
        $accountTypeLabel = self::ACCOUNT_TYPE_LABELS[$data['account_type']] ?? $data['account_type'];
        $accountName = $data['bank_name'].' - '.$accountTypeLabel;

        return new self(
            accountId: (int) $data['account_id'],
            accountName: $accountName,
            referenceMonth: $data['reference_month'],
            creditConciliated: (int) ($data['credit_conciliated'] ?? 0),
            creditNotConciliated: (int) ($data['credit_not_conciliated'] ?? 0),
            debitConciliated: (int) ($data['debit_conciliated'] ?? 0),
            debitNotConciliated: (int) ($data['debit_not_conciliated'] ?? 0)
        );
    }
}
