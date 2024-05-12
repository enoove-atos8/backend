<?php

namespace App\Domain\Financial\Reviewers\Interfaces;

use Illuminate\Support\Collection;

interface FinancialReviewerRepositoryInterface
{
    public function getFinancialReviewers(): Collection;
}
