<?php

namespace App\Domain\Financial\Entries\Reports\Models;

use App\Domain\Accounts\Users\Models\User;
use App\Domain\Financial\Reviewers\Models\FinancialReviewer;
use Domain\Ecclesiastical\Groups\Models\Group;
use Domain\Secretary\Membership\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by', 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_received_id', 'id');
    }
}
