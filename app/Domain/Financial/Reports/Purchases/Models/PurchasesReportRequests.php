<?php

namespace App\Domain\Financial\Reports\Purchases\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasesReportRequests extends Model
{
    protected $table = 'purchases_reports_request';


    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dates' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'card_id',
        'started_by',
        'report_name',
        'detailed_report',
        'generation_date',
        'dates',
        'status',
        'error',
        'link_report',
        'date_order',
        'all_cards_receipts',
        'amount',
    ];
}
