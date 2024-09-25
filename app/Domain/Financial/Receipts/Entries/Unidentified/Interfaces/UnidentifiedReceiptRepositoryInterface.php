<?php

namespace Domain\Financial\Receipts\Entries\Unidentified\Interfaces;

use Domain\Financial\Receipts\Entries\Unidentified\DataTransferObjects\UnidentifiedReceiptData;
use Domain\Financial\Receipts\Entries\Unidentified\Models\UnidentifiedReceipts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface UnidentifiedReceiptRepositoryInterface
{
    public function createUnidentifiedReceipts(UnidentifiedReceiptData $unidentifiedReceiptData): UnidentifiedReceipts;
    public function deleteUnidentifiedReceipts(): bool;
    public function getUnidentifiedReceipts(int $id = null): Collection;


}
