<?php

namespace App\Domain\Financial\Exits\Reports\Models;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
