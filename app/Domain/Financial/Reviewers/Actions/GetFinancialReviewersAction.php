<?php

namespace App\Domain\Financial\Reviewers\Actions;

use App\Domain\Financial\Reviewers\Constants\ReturnMessages;
use App\Domain\Financial\Reviewers\Interfaces\FinancialReviewerRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class GetFinancialReviewersAction
{
    private FinancialReviewerRepository $financialReviewerRepository;

    public function __construct(FinancialReviewerRepositoryInterface $financialReviewerRepositoryInterface
    )
    {
        $this->financialReviewerRepository = $financialReviewerRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(): Collection
    {
        $reviewers = $this->financialReviewerRepository->getFinancialReviewers();

        if($reviewers->count() > 0)
        {
            return $reviewers;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_REVIEWERS_NOT_FOUND, 404);
        }
    }
}
