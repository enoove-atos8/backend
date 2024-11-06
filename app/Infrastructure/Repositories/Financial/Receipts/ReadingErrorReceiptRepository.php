<?php

namespace Infrastructure\Repositories\Financial\Receipts;

use Domain\Financial\Receipts\Entries\ReadingError\DataTransferObjects\ReadingErrorReceiptData;
use Domain\Financial\Receipts\Entries\ReadingError\Interfaces\ReadingErrorReceiptRepositoryInterface;
use Domain\Financial\Receipts\Entries\ReadingError\Models\ReadingErrorReceipt;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class ReadingErrorReceiptRepository extends BaseRepository implements ReadingErrorReceiptRepositoryInterface
{
    protected mixed $model = ReadingErrorReceipt::class;

    const ENTRY_TYPE_COLUMN = 'entry_type';
    const REASON_COLUMN = 'reason';
    const AMOUNT_COLUMN = 'amount';
    const DELETED_COLUMN = 'deleted';
    const RECEIPT_LINK_COLUMN = 'receipt_link';


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param ReadingErrorReceiptData $readingErrorReceiptData
     * @return ReadingErrorReceipt
     */
    public function createReadingErrorReceipt(ReadingErrorReceiptData $readingErrorReceiptData): ReadingErrorReceipt
    {
        return $this->create([
            'group_returned_id'     =>   $readingErrorReceiptData->groupReturnedId,
            'group_received_id'     =>   $readingErrorReceiptData->groupReceivedId,
            'entry_type'            =>   $readingErrorReceiptData->entryType,
            'amount'                =>   floatval($readingErrorReceiptData->amount),
            'institution'           =>   $readingErrorReceiptData->institution,
            'reason'                =>   $readingErrorReceiptData->reason,
            'devolution'            =>   $readingErrorReceiptData->devolution,
            'deleted'               =>   $readingErrorReceiptData->deleted,
            'receipt_link'          =>   $readingErrorReceiptData->receiptLink,
        ]);
    }



    /**
     *
     * @throws BindingResolutionException
     */
    public function getReadingErrorReceipts(string $reason): Collection
    {
        $this->queryConditions = [];

        $this->queryConditions [] = $this->whereEqual(self::DELETED_COLUMN, 0, 'and');
        $this->queryConditions [] = $this->whereEqual(self::REASON_COLUMN, $reason, 'and');

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }
}
