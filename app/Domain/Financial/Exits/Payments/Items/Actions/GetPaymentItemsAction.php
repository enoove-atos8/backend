<?php

namespace Domain\Financial\Exits\Payments\Items\Actions;

use App\Domain\Financial\Exits\Payments\Items\Constants\ReturnMessages;
use Domain\Financial\Exits\Payments\Items\Interfaces\PaymentItemRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentItemRepository;
use Throwable;

class GetPaymentItemsAction
{
    private PaymentItemRepository $paymentItemRepository;

    public function __construct(
        PaymentItemRepositoryInterface $paymentItemRepositoryInterface,
    )
    {
        $this->paymentItemRepository = $paymentItemRepositoryInterface;
    }

    /**
     * @throws Throwable
     * @throws BindingResolutionException|GeneralExceptions
     */
    public function execute(int $id): Collection
    {
        $items = $this->paymentItemRepository->getPaymentItems($id);

        if(count($items) > 0)
            return $items;
        else
            throw new GeneralExceptions(ReturnMessages::PAYMENTS_ITEMS_NOT_FOUND, 404);
    }
}
