<?php

namespace App\Domain\Accounts\TenantVerification\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'avatar',
        'type',
        'title',
        'gender',
        'phone',
        'address',
        'district',
        'city',
        'country',
        'birthday',
    ];
}
