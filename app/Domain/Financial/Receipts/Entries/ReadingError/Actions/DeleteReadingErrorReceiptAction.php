<?php

namespace Domain\Financial\Receipts\Entries\ReadingError\Actions;

use App\Infrastructure\Repositories\Financial\Entries\Automation\AutomationRepository;
use Domain\Financial\Receipts\Entries\ReadingError\Interfaces\ReadingErrorReceiptRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

class DeleteReadingErrorReceiptAction
{
    private AutomationRepository $readingErrorReceiptRepository;

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
