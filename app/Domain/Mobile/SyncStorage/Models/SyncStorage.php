<?php

namespace Domain\Mobile\SyncStorage\Models;

use Illuminate\Database\Eloquent\Model;

class SyncStorage extends Model
{
    /**
     * @var string
     */
    protected $table = 'sync_storage';


    protected $fillable = [
        'tenant',
        'module',
        'doc_type',
        'doc_sub_type',
        'division_id',
        'group_id',
        'payment_category_id',
        'payment_item_id',
        'is_payment',
        'is_credit_card_purchase',
        'credit_card_due_date',
        'number_installments',
        'purchase_credit_card_date',
        'purchase_credit_card_amount',
        'status',
        'path',
    ];
}
