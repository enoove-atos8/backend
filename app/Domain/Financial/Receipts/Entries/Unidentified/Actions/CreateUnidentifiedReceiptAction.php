<?php

namespace Domain\Financial\Receipts\Entries\Unidentified\Actions;

use Domain\Financial\Receipts\Entries\Unidentified\DataTransferObjects\UnidentifiedReceiptData;
use Domain\Financial\Receipts\Entries\Unidentified\Interfaces\UnidentifiedReceiptRepositoryInterface;
use Domain\Financial\Receipts\Entries\Unidentified\Models\UnidentifiedReceipts;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\Financial\Receipts\UnidentifiedReceiptsRepository;

class CreateUnidentifiedReceiptAction
{
    private UnidentifiedReceiptsRepository $unidentifiedReceiptsRepository;

    public function __construct(UnidentifiedReceiptRepositoryInterface $unidentifiedReceiptsRepositoryInterface)
    {
        $this->unidentifiedReceiptsRepository = $unidentifiedReceiptsRepositoryInterface;
    }



    public function __invoke(UnidentifiedReceiptData $unidentifiedReceiptData): UnidentifiedReceipts | bool
    {
        $unidentifiedReceipt = $this->unidentifiedReceiptsRepository->createUnidentifiedReceipts($unidentifiedReceiptData);

        if(!is_null($unidentifiedReceipt->id))
        {
            return $unidentifiedReceipt;
        }
        else
        {
            return false;
        }
    }
}
