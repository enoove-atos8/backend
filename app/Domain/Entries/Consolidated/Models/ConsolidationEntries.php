<?php

namespace Domain\Entries\Consolidated\Models;

use Illuminate\Database\Eloquent\Model;

class ConsolidationEntries extends Model
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
        'amount',
    ];
}
