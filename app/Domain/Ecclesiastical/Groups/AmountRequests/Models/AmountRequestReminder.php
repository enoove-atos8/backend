<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmountRequestReminder extends Model
{
    protected $table = 'amount_request_reminders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amount_request_id',
        'type',
        'channel',
        'scheduled_at',
        'sent_at',
        'status',
        'error_message',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function amountRequest(): BelongsTo
    {
        return $this->belongsTo(AmountRequest::class);
    }
}
