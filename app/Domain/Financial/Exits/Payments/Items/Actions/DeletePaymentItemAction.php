<?php

namespace Domain\Financial\Exits\Payments\Items\Actions;

use App\Domain\Financial\Exits\Payments\Items\Constants\ReturnMessages;
use Domain\Financial\Exits\Payments\Items\Interfaces\PaymentItemRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentItemRepository;
use Throwable;

class DeletePaymentItemAction
{
    private PaymentItemRepositoryInterface $paymentItemRepository;

    public function __construct(
        PaymentItemRepositoryInterface $paymentItemRepositoryInterface,
    )
    {
        $this->paymentItemRepository = $paymentItemRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(int $id): bool
    {
        $deleted = $this->paymentItemRepository->deletePaymentItem($id);

        if($deleted)
            return $deleted;
        else
            throw new GeneralExceptions(ReturnMessages::DELETED_PAYMENTS_ITEMS_ERROR, 500);
    }
}
