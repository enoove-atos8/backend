<?php

namespace Domain\Entries\Actions;

use Domain\Entries\Constants\ReturnMessages;
use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Entries\Models\Entry;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\EntryRepository;
use Throwable;

class GetAmountByEntryTypeAction
{
    private EntryRepository $entryRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function __invoke($rangeMonthlyDate, $amountType, $entryType)
    {
        $entries = $this->entryRepository->getAmountByEntryType($rangeMonthlyDate, $amountType, $entryType);
        $total = $entries->sum(EntryRepository::AMOUNT_COLUMN);

        if(count($entries) !== 0 and $total !== 0)
        {
            return $total;
        }
        elseif (count($entries) == 0 and $total == 0)
        {
            throw new GeneralExceptions(ReturnMessages::INFO_AMOUNT_BY_ENTRY_TYPE_NO_RECORDS, 404);
        }
    }
}
