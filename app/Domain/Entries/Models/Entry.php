<?php

namespace Domain\Entries\Models;

use Domain\Members\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
