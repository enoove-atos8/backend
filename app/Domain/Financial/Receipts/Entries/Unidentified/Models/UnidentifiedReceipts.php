<?php

namespace Domain\Financial\Receipts\Entries\Unidentified\Models;

use Illuminate\Database\Eloquent\Model;

class UnidentifiedReceipts extends Model
{
    protected $table = 'unidentified_receipts';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entry_type',
        'amount',
        'deleted',
        'receipt_link',
    ];
}
