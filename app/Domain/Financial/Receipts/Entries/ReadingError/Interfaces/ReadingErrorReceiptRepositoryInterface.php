<?php

namespace Domain\Financial\Receipts\Entries\ReadingError\Interfaces;

use Domain\Financial\Receipts\Entries\ReadingError\DataTransferObjects\ReadingErrorReceiptData;
use Domain\Financial\Receipts\Entries\ReadingError\Models\ReadingErrorReceipt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ReadingErrorReceiptRepositoryInterface
{
    public function createReadingErrorReceipt(ReadingErrorReceiptData $readingErrorReceiptData): ReadingErrorReceipt;
    public function getReadingErrorReceipts(string $reason): Collection;
    public function deleteReceipt(int $id): mixed;
}
