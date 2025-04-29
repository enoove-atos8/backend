<?php

namespace Domain\Financial\ReceiptProcessing\Actions;

use Domain\Financial\ReceiptProcessing\DataTransferObjects\ReceiptProcessingData;
use Domain\Financial\ReceiptProcessing\Interfaces\ReceiptProcessingRepositoryInterface;
use Domain\Financial\ReceiptProcessing\Models\ReceiptProcessing;
use Infrastructure\Repositories\Financial\ReceiptProcessing\ReceiptProcessingRepository;

class CreateReceiptProcessing
{
    private ReceiptProcessingRepositoryInterface $receiptProcessingRepository;

    public function __construct(ReceiptProcessingRepositoryInterface $receiptProcessingRepositoryInterface)
    {
        $this->receiptProcessingRepository = $receiptProcessingRepositoryInterface;
    }


    public function execute(ReceiptProcessingData $receiptProcessingData): ReceiptProcessing
    {
        return $this->receiptProcessingRepository->createReceiptProcessing($receiptProcessingData);
    }
}
