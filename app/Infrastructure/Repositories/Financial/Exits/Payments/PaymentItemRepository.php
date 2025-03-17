<?php

namespace Infrastructure\Repositories\Financial\Exits\Payments;

use Infrastructure\Repositories\BaseRepository;

class PaymentItemRepository extends BaseRepository
{
    const TABLE_NAME = 'payment_item';
    const ID_COLUMN_JOINED = 'payment_item.id';

    const DISPLAY_SELECT_COLUMNS = [
        'payment_item.id as payment_item_id',
        'payment_item.payment_category_id as payment_item_payment_category_id',
        'payment_item.slug as payment_item_slug',
        'payment_item.name as payment_item_name',
    ];
}
