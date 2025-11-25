<?php

namespace Domain\CentralDomain\Plans\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'activated',
        'stripe_product_id',
        'stripe_price_id',
        'billing_unit',
        'billing_interval',
        'trial_period_days',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'trial_period_days' => 'integer',
        'price' => 'float',
        'activated' => 'boolean',
        'billing_unit' => 'boolean',
    ];
}
