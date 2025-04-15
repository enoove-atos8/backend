<?php

namespace Domain\Financial\Exits\Payments\Categories\Actions;

use App\Domain\Financial\Exits\Payments\Categories\Constants\ReturnMessages;
use Domain\Financial\Exits\Payments\Categories\Interfaces\PaymentCategoryRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentCategoryRepository;
use PHPUnit\Event\Code\Throwable;

class GetPaymentsAction
{
    private PaymentCategoryRepository $paymentCategoryRepository;

    public function __construct(
        PaymentCategoryRepositoryInterface $paymentCategoryRepositoryInterface,
    )
    {
        $this->paymentCategoryRepository = $paymentCategoryRepositoryInterface;
    }

    /**
     * @throws Throwable
     * @throws BindingResolutionException|GeneralExceptions
     */
    public function execute(): Collection | Paginator
    {
        $payments = $this->paymentCategoryRepository->getPayments();

        if(count($payments) > 0)
            return $payments;
        else
            throw new GeneralExceptions(ReturnMessages::PAYMENTS_CATEGORY_NOT_FOUND, 404);
    }
}
