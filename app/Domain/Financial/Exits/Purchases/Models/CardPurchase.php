<?php

namespace App\Domain\Financial\Exits\Purchases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardPurchase extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cards_purchases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'card_id',
        'status',
        'amount',
        'installments',
        'installment_amount',
        'date',
        'deleted',
        'receipt',
    ];

}
