<?php

namespace Domain\Financial\Exits\Payments\Categories\Interfaces;

use Illuminate\Support\Collection;

interface PaymentCategoryRepositoryInterface
{
    public function getPayments(): Collection;
}
