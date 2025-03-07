<?php

namespace Infrastructure\Repositories\Mobile\SyncStorage;

use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Domain\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use App\Domain\SyncStorage\Models\SyncStorage;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class SyncStorageRepository extends BaseRepository implements SyncStorageRepositoryInterface
{
    protected mixed $model = SyncStorage::class;

    const TABLE_NAME = 'sync_storage';

    const STATUS_COLUMN = 'status';
    const DOC_TYPE_COLUMN = 'doc_type';
    const ENTRIES_VALUE_DOC_TYPE = 'entries';
    const EXITS_VALUE_DOC_TYPE = 'exits';
    const TO_PROCESS_VALUE = 'to_process';
    const DONE_VALUE = 'done';

    const ERROR_VALUE = 'error';

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
            'tenant'                        => $syncStorageData->tenant,
            'module'                        => $syncStorageData->module,
            'doc_type'                      => $syncStorageData->docType,
            'doc_sub_type'                  => $syncStorageData->docSubType,
            'division_id'                   => $syncStorageData->divisionId,
            'group_id'                      => $syncStorageData->groupId,
            'payment_category_id'           => $syncStorageData->paymentCategoryId,
            'payment_item_id'               => $syncStorageData->paymentItemId,
            'is_payment'                    => $syncStorageData->isPayment,
            'is_devolution'                 => $syncStorageData->isDevolution,
            'is_credit_card_purchase'       => $syncStorageData->isCreditCardPurchase,
            'credit_card_due_date'          => $syncStorageData->creditCardDueDate,
            'number_installments'           => $syncStorageData->numberInstallments,
            'purchase_credit_card_date'     => $syncStorageData->purchaseCreditCardDate,
            'purchase_credit_card_amount'   => $syncStorageData->purchaseCreditCardAmount,
            'status'                        => $syncStorageData->status,
            'path'                          => $syncStorageData->path
        ]);
    }



    /**
     * @param string $docType
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getSyncStorageData(string $docType): Collection
    {
        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::STATUS_COLUMN, self::TO_PROCESS_VALUE, 'and');
        $this->queryConditions [] = $this->whereEqual(self::DOC_TYPE_COLUMN, $docType, 'and');

        return $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::ID_COLUMN,
            BaseRepository::ORDERS['ASC']
        );
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
