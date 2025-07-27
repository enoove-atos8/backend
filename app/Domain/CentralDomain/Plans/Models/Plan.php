<?php

namespace Domain\CentralDomain\Plans\Models;

use App\Domain\Financial\Entries\Entries\Models\Entry;
use App\Domain\Financial\Reviewers\Models\FinancialReviewer;
use Domain\Secretary\Membership\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $table = 'plans';



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'activated',
    ];
}
