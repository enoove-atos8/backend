<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountsMovements extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts_movements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'account_id',
        'file_id',
        'movement_date',
        'transaction_type',
        'description',
        'amount',
        'movement_type',
        'anonymous',
        'conciliated_status',
    ];
}
