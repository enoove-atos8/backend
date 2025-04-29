<?php

namespace Domain\Financial\ReceiptProcessing\Actions;

use Domain\Financial\ReceiptProcessing\Interfaces\ReceiptProcessingRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\Financial\ReceiptProcessing\ReceiptProcessingRepository;

class DeleteReceiptProcessedAction
{
    private ReceiptProcessingRepositoryInterface $receiptProcessingRepository;

    public function __construct(ReceiptProcessingRepositoryInterface $receiptProcessingRepositoryInterface)
    {
        $this->receiptProcessingRepository = $receiptProcessingRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     */
    public function execute($id)
    {
        return $this->receiptProcessingRepository->deleteReceiptsProcessed($id);
    }
}
