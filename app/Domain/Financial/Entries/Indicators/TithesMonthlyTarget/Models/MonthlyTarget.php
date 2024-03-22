<?php

namespace Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyTarget extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entry_type',
        'transaction_type',
        'transaction_compensation',
        'date_transaction_compensation',
        'date_entry_register',
        'amount',
        'recipient',
        'member_id',
        'reviewer_id',
        'deleted',
        'devolution',
        'comments',
        'receipt_link',
    ];
}
