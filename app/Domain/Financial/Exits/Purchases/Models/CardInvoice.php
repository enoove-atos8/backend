<?php

namespace App\Domain\Financial\Exits\Purchases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardInvoice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cards_invoices';

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
        'reference_date',
        'payment_date',
        'payment_method',
        'is_closed',
        'deleted',
    ];
}
