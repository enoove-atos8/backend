<?php

namespace Domain\Financial\AccountsAndCards\Cards\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'card_number',
        'expiry_date',
        'closing_date',
        'status',
        'active',
        'credit_card_brand',
        'person_type',
        'card_holder_name',
        'limit',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'active' => 'boolean',
        'limit' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the formatted card number (masked except for last 4 digits).
     *
     * @return string
     */
    public function getFormattedCardNumberAttribute()
    {
        $cardNumber = $this->card_number;
        $lastFourDigits = substr($cardNumber, -4);
        
        return str_repeat('*', strlen($cardNumber) - 4) . $lastFourDigits;
    }
}
