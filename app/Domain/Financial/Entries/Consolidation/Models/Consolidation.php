<?php

namespace App\Domain\Financial\Entries\Consolidation\Models;

use Illuminate\Database\Eloquent\Model;

class Consolidation extends Model
{
    protected $table = 'consolidation_entries';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'consolidated',
        'designated_amount',
        'offers_amount',
        'tithe_amount',
        'total_amount',
        'monthly_target',
    ];
}
