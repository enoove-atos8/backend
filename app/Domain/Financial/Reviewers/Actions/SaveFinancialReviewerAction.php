<?php

namespace App\Domain\Financial\Reviewers\Actions;

use App\Domain\Financial\Reviewers\Interfaces\FinancialReviewerRepositoryInterface;

class SaveFinancialReviewerAction
{
    public function __construct(
        private FinancialReviewerRepositoryInterface $financialReviewerRepository
    ) {}

    /**
     * Salva uma coleção de revisores financeiros em lote
     *
     * @param  FinancialReviewerData[]  $reviewersData
     */
    public function execute(array $reviewersData): bool
    {
        return $this->financialReviewerRepository->batchCreateReviewers($reviewersData);
    }
}
