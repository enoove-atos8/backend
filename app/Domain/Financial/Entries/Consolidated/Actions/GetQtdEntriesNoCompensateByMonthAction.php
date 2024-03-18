<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;

use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\BaseRepository;

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
