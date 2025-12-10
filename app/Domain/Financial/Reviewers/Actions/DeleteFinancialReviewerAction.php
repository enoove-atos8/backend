<?php

namespace App\Domain\Financial\Reviewers\Actions;

use App\Domain\Financial\Reviewers\Interfaces\FinancialReviewerRepositoryInterface;

class DeleteFinancialReviewerAction
{
    public function __construct(
        private FinancialReviewerRepositoryInterface $financialReviewerRepository
    ) {}

    /**
     * Deleta (soft delete) um revisor financeiro pelo ID
     */
    public function execute(int $id): bool
    {
        return $this->financialReviewerRepository->deleteReviewer($id);
    }
}
