<?php

namespace Domain\Financial\Entries\Indicators\AmountToCompensate\Interfaces;

use Illuminate\Support\Collection;

interface AmountToCompensateRepositoryInterface
{
    public function getEntriesAmountToCompensate(): Collection;

}
