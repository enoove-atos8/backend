<?php

namespace App\Domain\Financial\Reports\Exits\Models;

use Illuminate\Database\Eloquent\Model;

class ExitsReportRequests extends Model
{
    protected $table = 'exits_reports_request';


    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dates' => 'array',
        'exit_types' => 'array',
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
        'detailed_report',
        'generation_date',
        'dates',
        'status',
        'error',
        'exit_types',
        'link_report',
        'date_order',
    ];
}
