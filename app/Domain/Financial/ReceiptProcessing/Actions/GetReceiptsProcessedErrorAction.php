<?php

namespace Domain\Financial\ReceiptProcessing\Actions;

use Domain\Financial\ReceiptProcessing\DataTransferObjects\ReceiptProcessingData;
use Domain\Financial\ReceiptProcessing\Interfaces\ReceiptProcessingRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Financial\ReceiptProcessing\ReceiptProcessingRepository;

class GetReceiptsProcessedErrorAction
{
    private ReceiptProcessingRepository $receiptProcessingRepository;

    public function __construct(ReceiptProcessingRepositoryInterface $receiptProcessingRepositoryInterface)
    {
        $this->receiptProcessingRepository = $receiptProcessingRepositoryInterface;
    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(string $docType, string $status): Collection | Paginator | null
    {
        $receiptsProcessing = $this->receiptProcessingRepository->getReceiptsProcessed($docType, $status);

        if(count($receiptsProcessing) > 0)
            return $receiptsProcessing;
        else
            return null;
    }
}
