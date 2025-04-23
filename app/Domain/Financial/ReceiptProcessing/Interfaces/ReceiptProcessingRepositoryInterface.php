<?php

namespace Domain\Financial\ReceiptProcessing\Interfaces;

use Domain\Financial\ReceiptProcessing\DataTransferObjects\ReceiptProcessingData;
use Domain\Financial\ReceiptProcessing\Models\ReceiptProcessing;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface ReceiptProcessingRepositoryInterface
{
    public function createReceiptProcessing(ReceiptProcessingData $receiptProcessingData): ReceiptProcessing;

    public function getReceiptsProcessed(string $docType, string $status): Collection;

    public function deleteReceiptsProcessed(int $id): mixed;
}
