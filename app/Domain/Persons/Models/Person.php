<?php

namespace App\Domain\Persons\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'avatar',
        'gender',
        'birth_date',
        'cpf',
        'rg',
        'cell_phone',
        'ministry',
        'department',
        'responsibility',
    ];


    /**
     * Function that return the user
     *
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
