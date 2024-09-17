<?php

namespace App\Domain\Financial\Entries\General\Models;

use Domain\Members\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entry extends Model
{
    protected $table = 'entries';
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
        'ecclesiastical_divisions_groups_id',
        'member_id',
        'reviewer_id',
        'devolution',
        'ecclesiastical_divisions_groups_devolution_origin',
        'residual_value',
        'deleted',
        'comments',
        'receipt_link',
    ];


    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
