<?php

namespace Domain\Financial\ReceiptProcessing\Actions;

use Domain\Financial\ReceiptProcessing\Interfaces\ReceiptProcessingRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\ReceiptProcessing\ReceiptProcessingRepository;

class DeleteReceiptProcessedAction
{
    private ReceiptProcessingRepositoryInterface $receiptProcessingRepository;

    public function __construct(ReceiptProcessingRepositoryInterface $receiptProcessingRepositoryInterface)
    {
        $this->receiptProcessingRepository = $receiptProcessingRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions
     */
    public function execute($id)
    {

        $receiptDeleted = $this->receiptProcessingRepository->deleteReceiptsProcessed($id);

        if($receiptDeleted)
            return $receiptDeleted;

        else
            throw new GeneralExceptions('Houve um erro ao excluir este comprovante, tente mais tarde', 500);
    }
}
