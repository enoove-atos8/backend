<?php

namespace Infrastructure\Repositories\Financial\Exits\Payments;

use Domain\Financial\Exits\Payments\Categories\Interfaces\PaymentCategoryRepositoryInterface;
use Domain\Financial\Exits\Payments\Categories\Models\PaymentCategory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class PaymentCategoryRepository extends BaseRepository implements PaymentCategoryRepositoryInterface
{
    protected mixed $model = PaymentCategory::class;
    const TABLE_NAME = 'payment_category';
    const ID_COLUMN_JOINED = 'payment_category.id';
    const TABLE_ALIAS = 'payment_category';

    const DISPLAY_SELECT_COLUMNS = [
        'payment_category.id as payment_category_id',
        'payment_category.slug as payment_category_slug',
        'payment_category.name as payment_category_name',
        'payment_category.description as payment_category_description',
    ];



    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getPayments(): Collection
    {
        $this->queryConditions = [];

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }
}
