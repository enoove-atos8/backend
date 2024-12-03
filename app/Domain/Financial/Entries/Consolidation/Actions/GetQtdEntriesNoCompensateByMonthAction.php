<?php

namespace App\Domain\Financial\Entries\Consolidation\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
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
     * @return Collection
     * @throws BindingResolutionException
     */
    public function __invoke(string $dateRegister): Collection
    {
       return $this->entryRepository->getAllEntriesByDateAndType($dateRegister, 'register')
                                    ->where(EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN,
                                            BaseRepository::OPERATORS['EQUALS'],
                                            null);
    }

}
