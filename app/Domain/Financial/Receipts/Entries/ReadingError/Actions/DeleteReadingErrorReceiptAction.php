<?php

namespace Domain\Financial\Receipts\Entries\ReadingError\Actions;

use Domain\Financial\Receipts\Entries\ReadingError\DataTransferObjects\ReadingErrorReceiptData;
use Domain\Financial\Receipts\Entries\ReadingError\Interfaces\ReadingErrorReceiptRepositoryInterface;
use Domain\Financial\Receipts\Entries\ReadingError\Models\ReadingErrorReceipt;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\Financial\Receipts\ReadingErrorReceiptRepository;

class DeleteReadingErrorReceiptAction
{
    private ReadingErrorReceiptRepository $readingErrorReceiptRepository;

    public function __construct(ReadingErrorReceiptRepositoryInterface $readingErrorReceiptRepositoryInterface)
    {
        $this->readingErrorReceiptRepository = $readingErrorReceiptRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     */
    public function __invoke(int $id): bool
    {
        $deleted = $this->readingErrorReceiptRepository->deleteReceipt($id);

        if($deleted)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
