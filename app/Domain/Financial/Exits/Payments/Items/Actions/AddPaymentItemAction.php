<?php

namespace Domain\Financial\Exits\Payments\Items\Actions;

use App\Domain\Financial\Exits\Payments\Items\Constants\ReturnMessages;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Domain\Financial\Exits\Exits\Models\Exits;
use Domain\Financial\Exits\Payments\Items\Interfaces\PaymentItemRepositoryInterface;
use Domain\Financial\Exits\Payments\Items\Models\PaymentItem;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentItemRepository;

class AddPaymentItemAction
{
    private PaymentItemRepositoryInterface $paymentItemRepository;

    public function __construct(PaymentItemRepositoryInterface $paymentItemRepositoryInterface)
    {
        $this->paymentItemRepository = $paymentItemRepositoryInterface;
    }


    /**
     * @param PaymentItemData $paymentItemData
     * @return PaymentItem
     * @throws GeneralExceptions
     */
    public function execute(PaymentItemData $paymentItemData): PaymentItem
    {
        $paymentItem = $this->paymentItemRepository->addPaymentItem($paymentItemData);

        if(!is_null($paymentItem->id))
            return $paymentItem;
        else
            throw new GeneralExceptions(ReturnMessages::ADD_PAYMENTS_ITEMS_ERROR, 500);

    }
}
