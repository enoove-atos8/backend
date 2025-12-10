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
        'budget_value',
        'budget_type',
        'budget_activated',
    ];
}
