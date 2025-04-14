<?php

namespace Infrastructure\Repositories\Financial\Exits\Payments;

use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use Domain\Financial\Exits\Payments\Items\Interfaces\PaymentItemRepositoryInterface;
use Domain\Financial\Exits\Payments\Items\Models\PaymentItem;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class PaymentItemRepository extends BaseRepository implements PaymentItemRepositoryInterface
{
    protected mixed $model = PaymentItem::class;

    const TABLE_NAME = 'payment_item';
    const ID_COLUMN_JOINED = 'payment_item.id';
    const PAYMENT_CATEGORY_ID_COLUMN_JOINED = 'payment_item.payment_category_id';
    const TABLE_ALIAS = 'payment_item';

    const DISPLAY_SELECT_COLUMNS = [
        'payment_item.id as payment_item_id',
        'payment_item.payment_category_id as payment_item_payment_category_id',
        'payment_item.slug as payment_item_slug',
        'payment_item.name as payment_item_name',
        'payment_item.description as payment_item_description',
    ];


    /**
     * @param int $id
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getPaymentItems(int $id): Collection
    {
        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::PAYMENT_CATEGORY_ID_COLUMN_JOINED, $id, 'and');

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }



    /**
     * @param int $id
     * @return bool
     */
    public function deletePaymentItem(int $id): bool
    {
        return $this->delete($id);
    }



    public function addPaymentItem(PaymentItemData $paymentItemData): PaymentItem
    {
        return $this->create([
            'payment_category_id'     =>   $paymentItemData->paymentCategoryId,
            'slug'                    =>   $paymentItemData->slug,
            'name'                    =>   $paymentItemData->name,
            'description'             =>   $paymentItemData->description,
        ]);
    }
}
