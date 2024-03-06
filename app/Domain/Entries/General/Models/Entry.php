<?php

namespace Domain\Entries\General\Models;

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
        'member_id',
        'reviewer_id',
        'deleted',
        'devolution',
        'comments',
        'receipt_link',
    ];


    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }


    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }
}
