<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmountRequestReceipt extends Model
{
    protected $table = 'amount_request_receipts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amount_request_id',
        'amount',
        'description',
        'image_url',
        'receipt_date',
        'created_by',
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
            'amount' => 'decimal:2',
            'receipt_date' => 'date',
            'deleted' => 'boolean',
        ];
    }

    public function amountRequest(): BelongsTo
    {
        return $this->belongsTo(AmountRequest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
