<?php

namespace Domain\Entries\Actions;

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
        $entries = $this->entryRepository->getAmountByEntryTypeV2($rangeMonthlyDate, $amountType, $entryType);
        $total = $entries->sum(EntryRepository::AMOUNT_COLUMN);

        if($entries and $total)
        {
            return $total;
        }
        throw_if(!$entries, GeneralExceptions::class, 'Ocorreu um erro ao buscar uma entrada por tipo!', 500);

    }
}
