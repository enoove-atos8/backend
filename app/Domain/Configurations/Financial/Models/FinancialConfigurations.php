<?php

namespace Domain\Configurations\Financial\Models;

use Illuminate\Database\Eloquent\Model;
class FinancialConfigurations extends Model
{
    protected $table = 'financial_configurations';
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
