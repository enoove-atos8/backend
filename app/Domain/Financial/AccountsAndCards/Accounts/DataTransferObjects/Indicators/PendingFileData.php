<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators;

class PendingFileData
{
    public function __construct(
        public int $id,
        public int $accountId,
        public string $bankName,
        public string $originalFilename,
        public string $referenceDate,
        public string $status,
        public ?string $errorMessage,
        public string $createdAt
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            accountId: (int) $data['account_id'],
            bankName: $data['bank_name'],
            originalFilename: $data['original_filename'],
            referenceDate: $data['reference_date'],
            status: $data['status'],
            errorMessage: $data['error_message'] ?? null,
            createdAt: $data['created_at']
        );
    }
}
