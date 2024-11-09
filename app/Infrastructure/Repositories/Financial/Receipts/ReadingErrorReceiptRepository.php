<?php

namespace Infrastructure\Repositories\Financial\Receipts;

use Domain\Financial\Receipts\Entries\ReadingError\DataTransferObjects\ReadingErrorReceiptData;
use Domain\Financial\Receipts\Entries\ReadingError\Interfaces\ReadingErrorReceiptRepositoryInterface;
use Domain\Financial\Receipts\Entries\ReadingError\Models\ReadingErrorReceipt;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Member\MemberRepository;

class ReadingErrorReceiptRepository extends BaseRepository implements ReadingErrorReceiptRepositoryInterface
{
    protected mixed $model = ReadingErrorReceipt::class;

    const TABLE_NAME = 'reading_error_receipt';
    const GROUP_RETURNED_ID = 'reading_error_receipt.group_returned_id';
    const GROUP_RECEIVED_ID = 'reading_error_receipt.group_received_id';
    const ENTRY_TYPE_COLUMN = 'entry_type';
    const REASON_COLUMN = 'reason';
    const AMOUNT_COLUMN = 'amount';
    const DELETED_COLUMN = 'deleted';
    const RECEIPT_LINK_COLUMN = 'receipt_link';

    const GROUP_RECEIVED_ALIAS = 'g_received';
    const GROUP_RETURNED_ALIAS = 'g_returned';

    const DISPLAY_SELECT_COLUMNS = [
        'reading_error_receipt.id as reading_error_receipt_id',
        'reading_error_receipt.group_returned_id  as reading_error_receipt_group_returned_id',
        'reading_error_receipt.group_received_id as reading_error_receipt_group_received_id',
        'reading_error_receipt.entry_type as reading_error_receipt_entry_type',
        'reading_error_receipt.amount as reading_error_receipt_amount',
        'reading_error_receipt.reason as reading_error_receipt_reason',
        'reading_error_receipt.institution as reading_error_receipt_institution',
        'reading_error_receipt.devolution as reading_error_receipt_devolution',
        'reading_error_receipt.deleted as reading_error_receipt_deleted',
        'reading_error_receipt.receipt_link as reading_error_receipt_receipt_link',
    ];


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
     */
    public function getReadingErrorReceipts(string $reason): Collection
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            GroupsRepository::DISPLAY_SELECT_GROUP_WITH_RECEIVED_ALIAS,
            GroupsRepository::DISPLAY_SELECT_GROUP_WITH_RETURNED_ALIAS,
        );

        $this->queryConditions = [];
        //$this->requiredRelationships = ['ecclesiastical_divisions_groups'];


        $q = DB::table(self::TABLE_NAME)

            ->leftJoin(GroupsRepository::TABLE_NAME . ' as ' . self::GROUP_RECEIVED_ALIAS, self::GROUP_RECEIVED_ID,
                '=', self::GROUP_RECEIVED_ALIAS . '.id')

            ->leftJoin(GroupsRepository::TABLE_NAME . ' as ' . self::GROUP_RETURNED_ALIAS, self::GROUP_RETURNED_ID,
                '=', self::GROUP_RETURNED_ALIAS . '.id')

            ->where(self::TABLE_NAME . '.' . self::DELETED_COLUMN, 0)

            ->when($reason, function ($query) use ($reason) {
                if($reason != 'ALL')
                    return $query->where(self::TABLE_NAME . '.' . self::REASON_COLUMN, $reason);
            })
            ->select($displayColumnsFromRelationship);

        return $q->get();
    }




    /**
     * @throws BindingResolutionException
     */
    public function deleteReceipt(int $id): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $id,
            ];

        return $this->update($conditions, [
            'deleted'  =>   1,
        ]);
    }
}
