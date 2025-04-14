<?php

namespace Domain\Financial\ReceiptProcessing\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptProcessing extends Model
{
    protected $table = 'receipt_processing';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'doc_type',
        'doc_sub_type',
        'division_id',
        'group_returned_id',
        'group_received_id',
        'payment_category_id',
        'payment_item_id',
        'amount',
        'reason',
        'status',
        'institution',
        'devolution',
        'is_payment',
        'deleted',
        'receipt_link',
    ];

}
