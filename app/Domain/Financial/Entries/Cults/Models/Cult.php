<?php

namespace App\Domain\Financial\Entries\Cults\Models;

use Illuminate\Database\Eloquent\Model;

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
