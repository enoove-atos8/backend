<?php

namespace Domain\Financial\ReceiptProcessing\Actions;

use Domain\Financial\ReceiptProcessing\Constants\ReturnMessages;
use Domain\Financial\ReceiptProcessing\DataTransferObjects\ReceiptProcessingData;
use Domain\Financial\ReceiptProcessing\Interfaces\ReceiptProcessingRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
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
     * @throws GeneralExceptions
     */
    public function execute(string $docType, string $status): Collection | Paginator | null
    {
        $receiptsProcessing = $this->receiptProcessingRepository->getReceiptsProcessed($docType, $status);

        if(count($receiptsProcessing) > 0)
            return $receiptsProcessing;

        else
            throw new GeneralExceptions(ReturnMessages::NOT_FOUND, 404);
    }
}
