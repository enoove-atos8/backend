<?php

namespace App\Domain\Financial\Reviewers\Models;

use Domain\Secretary\Membership\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialReviewer extends Model
{
    protected $table = 'financial_reviewers';



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

    ];
}
