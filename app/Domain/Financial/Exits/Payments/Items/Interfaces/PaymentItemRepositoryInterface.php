<?php

namespace Domain\Financial\Exits\Payments\Items\Interfaces;

use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use Domain\Financial\Exits\Payments\Items\Models\PaymentItem;
use Illuminate\Support\Collection;

interface PaymentItemRepositoryInterface
{
    /**
     * @param int $id
     * @return Collection
     */
    public function getPaymentItems(int $id): Collection;


    /**
     * @param int $id
     * @return mixed
     */
    public function deletePaymentItem(int $id): mixed;


    /**
     * @param PaymentItemData $paymentItemData
     * @return PaymentItem
     */
    public function addPaymentItem(PaymentItemData $paymentItemData): PaymentItem;
}
