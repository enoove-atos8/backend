<?php

namespace Domain\Financial\Exits\Exits\Models;

use Illuminate\Database\Eloquent\Model;

class Exits extends Model
{
    protected $table = 'exits';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reviewer_id',
        'account_id',
        'exit_type',
        'division_id',
        'group_id',
        'payment_category_id',
        'payment_item_id',
        'is_payment',
        'deleted',
        'transaction_type',
        'transaction_compensation',
        'date_transaction_compensation',
        'date_exit_register',
        'timestamp_exit_transaction',
        'amount',
        'comments',
        'receipt_link',
    ];
}
