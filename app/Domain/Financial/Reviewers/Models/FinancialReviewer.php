<?php

namespace App\Domain\Financial\Reviewers\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReviewer extends Model
{
    protected $table = 'financial_reviewers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'reviewer_type',
        'avatar',
        'gender',
        'cpf',
        'rg',
        'email',
        'cell_phone',
        'activated',
        'deleted',
    ];
}
