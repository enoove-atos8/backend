<?php

namespace Domain\Financial\Reviewers\Actions;

use App\Domain\Financial\Reviewers\Interfaces\FinancialReviewerRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;

class GetReviewerAction
{
    private FinancialReviewerRepository $financialReviewerRepository;

    public function __construct(FinancialReviewerRepositoryInterface $financialReviewerRepositoryInterface)
    {
        $this->financialReviewerRepository = $financialReviewerRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     */
    public function __invoke(): ?Model
    {
        $reviewer = $this->financialReviewerRepository->getReviewer();

        return is_object($reviewer) ? $reviewer : null;
    }
}
