<?php

namespace Domain\Financial\Exits\Payments\Items\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    protected $table = 'payment_item';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'payment_category_id',
        'slug',
        'name',
        'description',
    ];
}
