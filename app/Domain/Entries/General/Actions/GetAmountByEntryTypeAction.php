<?php

namespace Domain\Entries\General\Actions;

use Domain\Entries\General\Constants\ReturnMessages;
use Domain\Entries\General\Interfaces\EntryRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Entries\General\EntryRepository;
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
        $totalGeneral = $entries->sum(EntryRepository::AMOUNT_COLUMN);
        $totalCompensated = $entries
                            ->where(
                                EntryRepository::COMPENSATED_COLUMN,
                                BaseRepository::OPERATORS['EQUALS'],
                                EntryRepository::COMPENSATED_VALUE)
                            ->sum(EntryRepository::AMOUNT_COLUMN);

        if(count($entries) !== 0 and $totalGeneral !== 0)
        {
            return [
                    'totalGeneral' => $totalGeneral,
                    'totalCompensated' => $totalCompensated
            ];
        }
        elseif (count($entries) == 0 and $totalGeneral == 0)
        {
            throw new GeneralExceptions(ReturnMessages::INFO_AMOUNT_BY_ENTRY_TYPE_NO_RECORDS, 404);
        }
    }
}
