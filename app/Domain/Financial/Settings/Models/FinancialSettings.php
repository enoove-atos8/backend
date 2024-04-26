<?php

namespace App\Domain\Financial\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialSettings extends Model
{
    protected $table = 'financial_settings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monthly_budget_tithes',
        'budget_activated',
    ];
}
