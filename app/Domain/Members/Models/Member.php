<?php

namespace Domain\Members\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'activated',
        'deleted',
        'avatar',
        'full_name',
        'member_type',
        'gender',
        'cpf',
        'rg',
        'work',
        'born_date',
        'email',
        'phone',
        'cell_phone',
        'address',
        'district',
        'city',
        'uf',
        'marital_status',
        'spouse',
        'father',
        'mother',
        'ecclesiastical_function',
        'ministries',
        'baptism_date',
        'blood_type',
        'education',
    ];
}
