<?php

namespace App\Domain\SyncStorage\Models;

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
        'card_id',
        'invoice_id',
        'invoice_closed_day',
        'payment_category_id',
        'payment_item_id',
        'is_payment',
        'is_devolution',
        'is_credit_card_purchase',
        'credit_card_due_date',
        'number_installments',
        'purchase_credit_card_date',
        'purchase_credit_card_amount',
        'purchase_credit_card_installment_amount',
        'credit_card_payment',
        'establishment_name',
        'purchase_description',
        'status',
        'path',
    ];
}
