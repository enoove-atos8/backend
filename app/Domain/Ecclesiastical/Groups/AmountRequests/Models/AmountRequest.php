<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Models;

use Illuminate\Database\Eloquent\Model;

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
}
