<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class AmountRequestIndicatorsData extends DataTransferObject
{
    public int $total;

    public string $totalAmount;

    public int $pending;

    public int $approved;

    public int $rejected;

    public int $transferred;

    public int $partiallyProven;

    public int $proven;

    public int $overdue;

    public int $closed;
}
