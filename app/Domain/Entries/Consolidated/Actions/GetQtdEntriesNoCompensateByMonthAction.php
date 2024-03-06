<?php

namespace Domain\Entries\Consolidated\Actions;

use Domain\Entries\Consolidated\Constants\ReturnMessages;
use Domain\Entries\General\Interfaces\EntryRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Entries\Consolidated\ConsolidatedEntriesRepository;
use Infrastructure\Repositories\Entries\General\EntryRepository;

class GetQtdEntriesNoCompensateByMonthAction
{
    private EntryRepository $entryRepository;


    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
    }


    /**
     * @param string $dateRegister
     * @return int
     * @throws BindingResolutionException
     */
    public function __invoke(string $dateRegister): int
    {
       return $this->entryRepository->getAllEntriesByDateAndType($dateRegister, 'register')
                                    ->where(EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN,
                                            BaseRepository::OPERATORS['EQUALS'],
                                            null)
                                    ->count();
    }

}
