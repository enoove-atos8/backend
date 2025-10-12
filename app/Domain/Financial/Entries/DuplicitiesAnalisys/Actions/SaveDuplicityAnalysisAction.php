<?php

namespace Domain\Financial\Entries\DuplicitiesAnalisys\Actions;

use App\Domain\Financial\Entries\Entries\Actions\DeleteEntryAction;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class SaveDuplicityAnalysisAction
{
    private EntryRepositoryInterface $entryRepository;
    private DeleteEntryAction $deleteEntryAction;


    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        DeleteEntryAction $deleteEntryAction
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->deleteEntryAction = $deleteEntryAction;
    }


    /**
     * @param array $entries
     * @return void
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function execute(array $entries): void
    {
        if(array_key_exists('kept', $entries))
        {
            if(count($entries['kept']) > 0)
            {
                foreach ($entries['kept'] as $entry)
                    $this->entryRepository->setDuplicityAnalysis($entry);
            }
        }

        if(array_key_exists('excluded', $entries))
        {
            if(count($entries['excluded']) > 0)
            {
                foreach ($entries['excluded'] as $entry)
                {
                    $this->entryRepository->setDuplicityAnalysis($entry);
                    $this->deleteEntryAction->execute($entry);
                }
            }
        }
    }
}
