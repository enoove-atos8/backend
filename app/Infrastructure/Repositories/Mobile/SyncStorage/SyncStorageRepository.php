<?php

namespace Infrastructure\Repositories\Mobile\SyncStorage;

use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Domain\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use App\Domain\SyncStorage\Models\SyncStorage;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class SyncStorageRepository extends BaseRepository implements SyncStorageRepositoryInterface
{
    protected mixed $model = SyncStorage::class;

    const TABLE_NAME = 'sync_storage';

    const STATUS_COLUMN = 'status';
    const DOC_TYPE_COLUMN = 'doc_type';
    const DOC_SUB_TYPE_COLUMN = 'doc_sub_type';
    const ENTRIES_VALUE_DOC_TYPE = 'entries';
    const EXITS_VALUE_DOC_TYPE = 'exits';
    const TO_PROCESS_VALUE = 'to_process';
    const IS_CREDIT_CARD_PURCHASE_COLUMN = 'is_credit_card_purchase';
    const PURCHASE_SUB_TYPE_VALUE = 'purchase';
    const DONE_VALUE = 'done';

    const ERROR_VALUE = 'error';
    const DUPLICATED_RECEIPT_VALUE = 'duplicated';
    const CLOSED_MONTH_VALUE = 'closed_month';

    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param SyncStorageData $syncStorageData
     * @return mixed
     */
    public function sendToDataServer(SyncStorageData $syncStorageData): SyncStorage
    {
        return $this->create([
            'tenant'                                        => $syncStorageData->tenant,
            'module'                                        => $syncStorageData->module,
            'doc_type'                                      => $syncStorageData->docType,
            'doc_sub_type'                                  => $syncStorageData->docSubType,
            'division_id'                                   => $syncStorageData->divisionId != '0' ? $syncStorageData->divisionId : null,
            'group_id'                                      => $syncStorageData->groupId != '0' ? $syncStorageData->groupId : null,
            'card_id'                                       => $syncStorageData->cardId,
            'payment_category_id'                           => $syncStorageData->paymentCategoryId != '0' ? $syncStorageData->paymentCategoryId : null,
            'payment_item_id'                               => $syncStorageData->paymentItemId != '0' ? $syncStorageData->paymentItemId : null,
            'is_payment'                                    => $syncStorageData->isPayment,
            'is_devolution'                                 => $syncStorageData->isDevolution,
            'is_credit_card_purchase'                       => $syncStorageData->isCreditCardPurchase,
            'closing_day'                                   => $syncStorageData->closingDay,
            'number_installments'                           => $syncStorageData->numberInstallments,
            'invoice_closed_day'                            => $syncStorageData->invoiceClosedDay,
            'purchase_credit_card_date'                     => $syncStorageData->purchaseCreditCardDate,
            'purchase_credit_card_amount'                   => $syncStorageData->purchaseCreditCardAmount,
            'purchase_credit_card_installment_amount'       => $syncStorageData->purchaseCreditCardInstallmentAmount,
            'status'                                        => $syncStorageData->status,
            'path'                                          => $syncStorageData->path
        ]);
    }


    /**
     * @param string $docType
     * @param string|null $docSubType
     * @param bool $getPurchases
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getSyncStorageData(string $docType, ?string $docSubType = null, bool $getPurchases = false): Collection
    {
        $query = function () use ($docType, $docSubType) {

            $q = DB::table(self::TABLE_NAME)
                ->where(self::STATUS_COLUMN, BaseRepository::OPERATORS['EQUALS'], self::TO_PROCESS_VALUE)
                ->where(self::DOC_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $docType);

            if(!is_null($docSubType))
                $q->where(self::DOC_SUB_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $docSubType);

            $q->orderBy(self::ID_COLUMN);


            $result = $q->get();
            return collect($result)->map(fn($item) => SyncStorageData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }


    /**
     * @param int $id
     * @param string $fileName
     * @return mixed
     * @throws BindingResolutionException
     */
    public function updatePathWithFileName(int $id, string $fileName): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $id,
            ];

        return $this->update($conditions, [
            'path'  =>   $fileName,
        ]);
    }


    /**
     * @param int $id
     * @param string $status
     * @return mixed
     * @throws BindingResolutionException
     */
    public function updateStatus(int $id, string $status): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $id,
            ];

        return $this->update($conditions, [
            'status'  =>   $status,
        ]);
    }
}
