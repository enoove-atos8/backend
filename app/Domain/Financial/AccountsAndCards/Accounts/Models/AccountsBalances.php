<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsBalances extends Model
{
    protected $table = 'accounts_balances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'reference_date',
        'previous_month_balance',
        'current_month_balance',
        'is_initial_balance',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_initial_balance' => 'boolean',
        'previous_month_balance' => 'decimal:2',
        'current_month_balance' => 'decimal:2',
    ];
}
