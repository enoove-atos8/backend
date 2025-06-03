<?php

namespace App\Domain\Financial\Exits\Purchases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardInstallment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cards_installments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'card_id',
        'invoice_id',
        'purchase_id',
        'status',
        'amount',
        'installment',
        'installment_amount',
        'date',
        'deleted',
    ];
}
