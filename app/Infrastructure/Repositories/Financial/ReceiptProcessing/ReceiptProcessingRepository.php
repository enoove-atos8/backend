<?php

namespace Infrastructure\Repositories\Financial\ReceiptProcessing;

use App\Domain\Financial\Reviewers\Models\FinancialReviewer;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Domain\Financial\ReceiptProcessing\DataTransferObjects\ReceiptProcessingData;
use Domain\Financial\ReceiptProcessing\Interfaces\ReceiptProcessingRepositoryInterface;
use Domain\Financial\ReceiptProcessing\Models\ReceiptProcessing;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentCategoryRepository;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentItemRepository;

class ReceiptProcessingRepository extends BaseRepository implements ReceiptProcessingRepositoryInterface
{
    protected mixed $model = ReceiptProcessing::class;

    const TABLE_NAME = 'receipt_processing';
    const GROUP_RETURNED_ID = 'receipt_processing.group_returned_id';
    const DOC_TYPE_COLUMN = 'receipt_processing.doc_type';
    const GROUP_RECEIVED_ID = 'receipt_processing.group_received_id';
    const DIVISION_ID = 'receipt_processing.division_id';
    const PAYMENT_CATEGORY_ID = 'receipt_processing.payment_category_id';
    const PAYMENT_ITEM_ID = 'receipt_processing.payment_item_id';
    const REVIEWER_ID = 'receipt_processing.reviewer_id';
    const REASON_COLUMN = 'reason';
    const STATUS_COLUMN = 'status';
    const ERROR_STATUS_VALUE = 'error';
    const AMOUNT_COLUMN = 'amount';
    const DELETED_COLUMN = 'receipt_processing.deleted';
    const RECEIPT_LINK_COLUMN = 'receipt_link';

    const GROUP_RECEIVED_ALIAS = 'g_received';
    const GROUP_RETURNED_ALIAS = 'g_returned';

    const DISPLAY_SELECT_COLUMNS = [
        'receipt_processing.id as receipt_processing_id',
        'receipt_processing.doc_type as receipt_processing_doc_type',
        'receipt_processing.doc_sub_type  as receipt_processing_doc_sub_type',
        'receipt_processing.reviewer_id  as receipt_processing_reviewer_id',
        'receipt_processing.division_id as receipt_processing_division_id',
        'receipt_processing.group_returned_id as receipt_processing_group_returned_id',
        'receipt_processing.group_received_id as receipt_processing_group_received_id',
        'receipt_processing.payment_category_id as receipt_processing_payment_category_id',
        'receipt_processing.payment_item_id as receipt_processing_payment_item_id',
        'receipt_processing.amount as receipt_processing_amount',
        'receipt_processing.reason as receipt_processing_reason',
        'receipt_processing.status as receipt_processing_status',
        'receipt_processing.institution as receipt_processing_institution',
        'receipt_processing.devolution as receipt_processing_devolution',
        'receipt_processing.is_payment as receipt_processing_is_payment',
        'receipt_processing.deleted as receipt_processing_deleted',
        'receipt_processing.transaction_type as receipt_processing_transaction_type',
        'receipt_processing.transaction_compensation as receipt_processing_transaction_compensation',
        'receipt_processing.date_transaction_compensation as receipt_processing_date_transaction_compensation',
        'receipt_processing.date_register as receipt_processing_date_register',
        'receipt_processing.receipt_link as receipt_processing_receipt_link',
    ];


    /**
     * @param ReceiptProcessingData $receiptProcessingData
     * @return ReceiptProcessing
     */
    public function createReceiptProcessing(ReceiptProcessingData $receiptProcessingData): ReceiptProcessing
    {
        return $this->create([
            'doc_type'                          =>   $receiptProcessingData->docType,
            'doc_sub_type'                      =>   $receiptProcessingData->docSubType,
            'reviewer_id'                       =>   $receiptProcessingData->reviewer->id,
            'division_id'                       =>   $receiptProcessingData->division->id,
            'group_returned_id'                 =>   $receiptProcessingData->groupReturned->id,
            'group_received_id'                 =>   $receiptProcessingData->groupReceived->id,
            'payment_category_id'               =>   $receiptProcessingData->paymentCategory->id,
            'payment_item_id'                   =>   $receiptProcessingData->paymentItem->id,
            'amount'                            =>   floatval($receiptProcessingData->amount),
            'reason'                            =>   $receiptProcessingData->reason,
            'status'                            =>   $receiptProcessingData->status,
            'institution'                       =>   $receiptProcessingData->institution,
            'devolution'                        =>   $receiptProcessingData->devolution,
            'is_payment'                        =>   $receiptProcessingData->isPayment,
            'deleted'                           =>   $receiptProcessingData->deleted,
            'transaction_type'                  =>   $receiptProcessingData->transactionType,
            'transaction_compensation'          =>   $receiptProcessingData->transactionCompensation,
            'date_transaction_compensation'     =>   $receiptProcessingData->dateTransactionCompensation,
            'date_register'                     =>   $receiptProcessingData->dateRegister,
            'receipt_link'                      =>   $receiptProcessingData->receiptLink,
        ]);
    }


    /**
     * @param string $docType
     * @param string $status
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getReceiptsProcessed(string $docType, string $status): Collection
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            DivisionRepository::DISPLAY_SELECT_COLUMNS,
            GroupsRepository::DISPLAY_SELECT_GROUP_WITH_RECEIVED_ALIAS,
            GroupsRepository::DISPLAY_SELECT_GROUP_WITH_RETURNED_ALIAS,
            PaymentCategoryRepository::DISPLAY_SELECT_COLUMNS,
            PaymentItemRepository::DISPLAY_SELECT_COLUMNS,
            FinancialReviewerRepository::DISPLAY_SELECT_COLUMNS,
        );


        $query = function () use ($docType, $status, $displayColumnsFromRelationship) {
            $q = DB::table(self::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(DivisionRepository::TABLE_NAME . ' as ' . DivisionRepository::TABLE_NAME, self::DIVISION_ID,
                    '=', DivisionRepository::TABLE_NAME . '.id')
                ->leftJoin(GroupsRepository::TABLE_NAME . ' as ' . self::GROUP_RECEIVED_ALIAS, self::GROUP_RECEIVED_ID,
                    '=', self::GROUP_RECEIVED_ALIAS . '.id')
                ->leftJoin(GroupsRepository::TABLE_NAME . ' as ' . self::GROUP_RETURNED_ALIAS, self::GROUP_RETURNED_ID,
                    '=', self::GROUP_RETURNED_ALIAS . '.id')
                ->leftJoin(PaymentCategoryRepository::TABLE_NAME . ' as ' . PaymentCategoryRepository::TABLE_NAME, self::PAYMENT_CATEGORY_ID,
                    '=', PaymentCategoryRepository::TABLE_ALIAS . '.id')
                ->leftJoin(PaymentItemRepository::TABLE_NAME . ' as ' . PaymentItemRepository::TABLE_ALIAS, self::PAYMENT_ITEM_ID,
                    '=', PaymentItemRepository::TABLE_ALIAS . '.id')
                ->leftJoin(FinancialReviewerRepository::TABLE_NAME . ' as ' . FinancialReviewerRepository::TABLE_NAME, self::REVIEWER_ID,
                    '=', FinancialReviewerRepository::TABLE_NAME . '.id')
                ->where(self::DOC_TYPE_COLUMN, $docType)
                ->where(self::DELETED_COLUMN, 0)
                ->where(self::STATUS_COLUMN, $status);


            $results = $q->get();
            return $results->map(fn($item) => ReceiptProcessingData::fromArray((array) $item));

        };

        return $this->doQuery($query);
    }


    /**
     * @param int $id
     * @return mixed
     * @throws BindingResolutionException
     */
    public function deleteReceiptsProcessed(int $id): mixed
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
