<?php

namespace Domain\Ecclesiastical\Groups\Models;

use Domain\Members\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $table = 'ecclesiastical_divisions_groups';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ecclesiastical_division_id',
        'parent_group_id',
        'leader_id',
        'name',
        'description',
        'financial_transactions_exists',
        'enabled',
        'temporary_event',
        'return_values',
        'return_receiving',
        'financial_group',
        'start_date',
        'end_date',
    ];


    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'ecclesiastical_division_group_id');
    }
}
