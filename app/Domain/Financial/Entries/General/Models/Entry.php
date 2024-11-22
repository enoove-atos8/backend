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
        'member_id',
        'reviewer_id',
        'cult_id',
        'group_returned_id',
        'group_received_id',
        'identification_pending',
        'entry_type',
        'transaction_type',
        'transaction_compensation',
        'date_transaction_compensation',
        'date_entry_register',
        'amount',
        'recipient',
        'timestamp_value_cpf',
        'devolution',
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
