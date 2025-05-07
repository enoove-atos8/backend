<?php

namespace Domain\Ecclesiastical\Divisions\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'ecclesiastical_divisions';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'route_resource',
        'name',
        'description',
        'enabled',
        'require_leader',
    ];
}
