<?php

namespace Infrastructure\Repositories\Financial\Exits\Payments;

use Infrastructure\Repositories\BaseRepository;

class PaymentCategoryRepository extends BaseRepository
{
    const TABLE_NAME = 'payment_category';
    const ID_COLUMN_JOINED = 'payment_category.id';

    const DISPLAY_SELECT_COLUMNS = [
        'payment_category.id as payment_category_id',
        'payment_category.slug as payment_category_slug',
        'payment_category.name as payment_category_name',
    ];
}
