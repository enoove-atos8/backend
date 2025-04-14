<?php

namespace Domain\Financial\Exits\Payments\Categories\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCategory extends Model
{
    protected $table = 'payment_category';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'slug',
        'name',
        'description',
    ];
}
