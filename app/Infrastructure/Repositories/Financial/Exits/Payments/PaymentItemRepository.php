<?php

namespace Infrastructure\Repositories\Financial\Exits\Payments;

use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use Domain\Financial\Exits\Payments\Items\Interfaces\PaymentItemRepositoryInterface;
use Domain\Financial\Exits\Payments\Items\Models\PaymentItem;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class PaymentItemRepository extends BaseRepository implements PaymentItemRepositoryInterface
{
    protected mixed $model = PaymentItem::class;

    const TABLE_NAME = 'payment_item';
    const ID_COLUMN_JOINED = 'payment_item.id';
    const PAYMENT_CATEGORY_ID_COLUMN_JOINED = 'payment_item.payment_category_id';
    const DELETED_COLUMN_JOINED = 'payment_item.deleted';
    const TABLE_ALIAS = 'payment_item';
    const PAGINATE_NUMBER = 999;

    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    const DISPLAY_SELECT_COLUMNS = [
        'payment_item.id as payment_item_id',
        'payment_item.payment_category_id as payment_item_payment_category_id',
        'payment_item.slug as payment_item_slug',
        'payment_item.name as payment_item_name',
        'payment_item.description as payment_item_description',
        'payment_item.deleted as payment_item_deleted',
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
        $this->queryConditions [] = $this->whereEqual(self::DELETED_COLUMN_JOINED, false, 'and');

        return $this->qbGetPaymentItems($this->queryConditions, self::DISPLAY_SELECT_COLUMNS, (array)self::ID_COLUMN_JOINED, false);
    }


    /**
     * @param int $id
     * @return bool
     * @throws BindingResolutionException
     */
    public function deletePaymentItem(int $id): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $id,
            ];

        return $this->update($conditions, [
            'deleted'  =>   true,
        ]);
    }



    public function addPaymentItem(PaymentItemData $paymentItemData): PaymentItem
    {
        return $this->create([
            'payment_category_id'     =>   $paymentItemData->paymentCategoryId,
            'slug'                    =>   $paymentItemData->slug,
            'name'                    =>   $paymentItemData->name,
            'description'             =>   $paymentItemData->description,
            'deleted'                 =>   $paymentItemData->deleted,
        ]);
    }



    /**
     * Get entries with members and reviewers joins
     *
     * @param array $queryClausesAndConditions
     * @param array $selectColumns
     * @param array $orderBy
     * @param bool $paginate
     * @param string $sort
     * @return Collection | Paginator
     * @throws BindingResolutionException
     */
    public function qbGetPaymentItems(
        array $queryClausesAndConditions,
        array $selectColumns,
        array $orderBy,
        bool $paginate = true,
        string $sort = 'desc'): Collection | Paginator
    {
        $query = function () use (
            $queryClausesAndConditions,
            $selectColumns,
            $orderBy,
            $sort,
            $paginate) {

            $q = DB::table(PaymentItemRepository::TABLE_NAME)
                ->select($selectColumns)
                ->where(function ($q) use($queryClausesAndConditions){
                    foreach ($queryClausesAndConditions as $key => $clause) {
                        $q->where($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                    }
                });

            if($paginate)
            {
                $paginator = $q->simplePaginate(self::PAGINATE_NUMBER);
                return $paginator->setCollection($paginator->getCollection()->map(fn($item) => PaymentItemData::fromArray((array) $item)));
            }
            else
            {
                $results = $q->get();
                return $results->map(fn($item) => PaymentItemData::fromArray((array) $item));
            }
        };

        return $this->doQuery($query);
    }
}
