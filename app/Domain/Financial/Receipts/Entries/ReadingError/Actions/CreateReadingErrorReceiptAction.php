<?php

namespace Domain\Financial\Receipts\Entries\ReadingError\Actions;

use App\Infrastructure\Repositories\Financial\Entries\Automation\AutomationRepository;
use Domain\Financial\Receipts\Entries\ReadingError\DataTransferObjects\ReadingErrorReceiptData;
use Domain\Financial\Receipts\Entries\ReadingError\Interfaces\ReadingErrorReceiptRepositoryInterface;
use Domain\Financial\Receipts\Entries\ReadingError\Models\ReadingErrorReceipt;

class CreateReadingErrorReceiptAction
{
    private AutomationRepository $readingErrorReceiptRepository;

    public function __construct(ReadingErrorReceiptRepositoryInterface $readingErrorReceiptRepositoryInterface)
    {
        $this->readingErrorReceiptRepository = $readingErrorReceiptRepositoryInterface;
    }



    public function __invoke(ReadingErrorReceiptData $readingErrorReceiptData): ReadingErrorReceipt | bool
    {
        $readingErrorReceipt = $this->readingErrorReceiptRepository->createReadingErrorReceipt($readingErrorReceiptData);

        if(!is_null($readingErrorReceipt->id))
        {
            return $readingErrorReceipt;
        }
        else
        {
            return false;
        }
    }
}
