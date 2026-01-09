<?php

namespace App\Domain\Financial\Entries\Entries\Models;

use App\Domain\Ecclesiastical\Groups\Groups\Models\Group;
use Domain\Secretary\Membership\Models\Member;
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
        'member_id',
        'account_id',
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
        'duplicity_verified',
        'timestamp_value_cpf',
        'devolution',
        'group_devolution',
        'residual_value',
        'deleted',
        'comments',
        'receipt_link',
    ];


    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_received_id', 'id');
    }
}
