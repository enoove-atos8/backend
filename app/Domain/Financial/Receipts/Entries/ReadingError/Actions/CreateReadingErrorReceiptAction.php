<?php

namespace Domain\Financial\Receipts\Entries\ReadingError\Actions;

use Domain\Financial\Receipts\Entries\ReadingError\DataTransferObjects\ReadingErrorReceiptData;
use Domain\Financial\Receipts\Entries\ReadingError\Interfaces\ReadingErrorReceiptRepositoryInterface;
use Domain\Financial\Receipts\Entries\ReadingError\Models\ReadingErrorReceipt;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\Financial\Receipts\ReadingErrorReceiptRepository;

class CreateReadingErrorReceiptAction
{
    private ReadingErrorReceiptRepository $readingErrorReceiptRepository;

    public function __construct(ReadingErrorReceiptRepositoryInterface $unidentifiedReceiptsRepositoryInterface)
    {
        $this->readingErrorReceiptRepository = $unidentifiedReceiptsRepositoryInterface;
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
