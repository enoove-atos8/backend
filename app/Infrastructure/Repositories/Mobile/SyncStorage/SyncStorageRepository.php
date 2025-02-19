<?php

namespace Infrastructure\Repositories\Mobile\SyncStorage;

use Domain\Mobile\SyncStorage\DataTransferObjects\SyncStorageData;
use Domain\Mobile\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use Domain\Mobile\SyncStorage\Models\SyncStorage;
use Infrastructure\Repositories\BaseRepository;

class SyncStorageRepository extends BaseRepository implements SyncStorageRepositoryInterface
{
    protected mixed $model = SyncStorage::class;

    const TABLE_NAME = 'sync_storage';


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
}
