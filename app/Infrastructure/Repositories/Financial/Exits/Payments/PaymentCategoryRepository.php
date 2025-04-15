<?php

namespace Infrastructure\Repositories\Financial\Exits\Payments;

use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use Domain\Financial\Exits\Payments\Categories\Interfaces\PaymentCategoryRepositoryInterface;
use Domain\Financial\Exits\Payments\Categories\Models\PaymentCategory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class PaymentCategoryRepository extends BaseRepository implements PaymentCategoryRepositoryInterface
{
    protected mixed $model = PaymentCategory::class;
    const TABLE_NAME = 'payment_category';
    const ID_COLUMN_JOINED = 'payment_category.id';
    const TABLE_ALIAS = 'payment_category';

    const PAGINATE_NUMBER = 999;

    /**
     * Array of conditions
     */
    private array $queryConditions = [];

    const DISPLAY_SELECT_COLUMNS = [
        'payment_category.id as payment_category_id',
        'payment_category.slug as payment_category_slug',
        'payment_category.name as payment_category_name',
        'payment_category.description as payment_category_description',
    ];


    /**
     * @return Collection|Paginator
     * @throws BindingResolutionException
     */
    public function getPayments(): Collection | Paginator
    {
        $this->queryConditions = [];

        return $this->qbGetPayments($this->queryConditions, self::DISPLAY_SELECT_COLUMNS, (array)self::ID_COLUMN_JOINED, false);
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
    public function qbGetPayments(
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

            $q = DB::table(PaymentCategoryRepository::TABLE_NAME)
                ->select($selectColumns);

            if($paginate)
            {
                $paginator = $q->simplePaginate(self::PAGINATE_NUMBER);
                return $paginator->setCollection($paginator->getCollection()->map(fn($item) => PaymentCategoryData::fromArray((array) $item)));
            }
            else
            {
                $results = $q->get();
                return $results->map(fn($item) => PaymentCategoryData::fromArray((array) $item));
            }
        };

        return $this->doQuery($query);
    }
}
