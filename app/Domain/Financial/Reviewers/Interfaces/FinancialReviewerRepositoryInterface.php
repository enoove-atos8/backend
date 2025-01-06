<?php

namespace App\Domain\Financial\Reviewers\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface FinancialReviewerRepositoryInterface
{
    public function getFinancialReviewers(): Collection;
    public function getReviewer(): Model | null;
}
