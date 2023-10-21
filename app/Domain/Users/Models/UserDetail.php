<?php

namespace Domain\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
