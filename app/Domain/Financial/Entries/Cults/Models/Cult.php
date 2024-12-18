<?php

namespace App\Domain\Financial\Entries\Cults\Models;

use App\Domain\Financial\Entries\Entries\Models\Entry;
use Domain\Members\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cult extends Model
{
    protected $table = 'cults';



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reviewer_id',
        'cult_day',
        'cult_date',
        'date_transaction_compensation',
        'transaction_type',
        'tithes_amount',
        'designated_amount',
        'offers_amount',
        'deleted',
        'receipt',
        'comments'
    ];
}
