<?php

namespace Domain\Financial\Receipts\Entries\ReadingError\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingErrorReceipt extends Model
{
    protected $table = 'reading_error_receipt';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_returned_id',
        'group_received_id',
        'entry_type',
        'amount',
        'institution',
        'reason',
        'devolution',
        'deleted',
        'receipt_link',
    ];
}
