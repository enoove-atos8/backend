<?php

namespace Infrastructure\Repositories\Financial\Receipts;

use Domain\Financial\Receipts\Entries\Unidentified\DataTransferObjects\UnidentifiedReceiptData;
use Domain\Financial\Receipts\Entries\Unidentified\Interfaces\UnidentifiedReceiptRepositoryInterface;
use Domain\Financial\Receipts\Entries\Unidentified\Models\UnidentifiedReceipts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class UnidentifiedReceiptsRepository extends BaseRepository implements UnidentifiedReceiptRepositoryInterface
{
    protected mixed $model = UnidentifiedReceipts::class;

    const ENTRY_TYPE_COLUMN = 'entry_type';
    const AMOUNT_COLUMN = 'amount';
    const DELETED_COLUMN = 'deleted';
    const RECEIPT_LINK_COLUMN = 'receipt_link';



    /**
     * @param int|null $id
     * @return Collection
     */
    public function getUnidentifiedReceipts(int $id = null): Collection
    {
        // TODO: Implement getUnidentifiedReceipts() method.
    }


    /**
     * @param UnidentifiedReceiptData $unidentifiedReceiptData
     * @return UnidentifiedReceipts
     */
    public function createUnidentifiedReceipts(UnidentifiedReceiptData $unidentifiedReceiptData): UnidentifiedReceipts
    {
        return $this->create([
            'entry_type'       =>   $unidentifiedReceiptData->entryType,
            'amount'           =>   floatval($unidentifiedReceiptData->amount),
            'deleted'          =>   $unidentifiedReceiptData->deleted,
            'receipt_link'     =>   $unidentifiedReceiptData->receiptLink,
        ]);
    }




    /**
     * @return bool
     */
    public function deleteUnidentifiedReceipts(): bool
    {
        // TODO: Implement deleteUnidentifiedReceipts() method.
    }
}
