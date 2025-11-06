<?php

namespace App\Domain\Financial\Reports\Balances\Models;

use Illuminate\Database\Eloquent\Model;

class BalancesReportRequests extends Model
{
    protected $table = 'balances_reports_request';

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
        'account_id',
        'started_by',
        'report_name',
        'generation_date',
        'dates',
        'status',
        'error',
        'link_report',
    ];
}
