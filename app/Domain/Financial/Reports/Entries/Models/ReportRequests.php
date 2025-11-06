<?php

namespace App\Domain\Financial\Reports\Entries\Models;

use Illuminate\Database\Eloquent\Model;

class ReportRequests extends Model
{
    protected $table = 'entries_report_requests';


    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dates' => 'array',
        'entry_types' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'group_received_id',
        'started_by',
        'report_name',
        'detailed_report',
        'generation_date',
        'dates',
        'status',
        'error',
        'entry_types',
        'date_order',
        'all_groups_receipts',
        'include_cash_deposit',
        'tithe_amount',
        'designated_amount',
        'offer_amount',
        'include_groups_entries',
        'include_anonymous_offers',
        'include_transfers_between_accounts',
    ];
}
