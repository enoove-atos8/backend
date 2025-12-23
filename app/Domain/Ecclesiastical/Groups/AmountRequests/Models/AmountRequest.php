<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Models;

use App\Domain\Financial\Entries\Entries\Models\Entry;
use App\Domain\Financial\Exits\Exits\Models\Exits;
use App\Models\User;
use Domain\Ecclesiastical\Groups\Models\Group;
use Domain\Secretary\Membership\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmountRequest extends Model
{
    protected $table = 'amount_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'group_id',
        'requested_amount',
        'description',
        'proof_deadline',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'transfer_exit_id',
        'transferred_at',
        'proven_amount',
        'devolution_entry_id',
        'devolution_amount',
        'closed_by',
        'closed_at',
        'requested_by',
        'deleted',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requested_amount' => 'decimal:2',
            'proven_amount' => 'decimal:2',
            'devolution_amount' => 'decimal:2',
            'proof_deadline' => 'date',
            'approved_at' => 'datetime',
            'transferred_at' => 'datetime',
            'closed_at' => 'datetime',
            'deleted' => 'boolean',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function transferExit(): BelongsTo
    {
        return $this->belongsTo(Exits::class, 'transfer_exit_id');
    }

    public function devolutionEntry(): BelongsTo
    {
        return $this->belongsTo(Entry::class, 'devolution_entry_id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(AmountRequestReceipt::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(AmountRequestReminder::class);
    }
}
