<?php

namespace Domain\Financial\Entries\DuplicitiesAnalisys\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;

class SaveDuplicityAnalysisAction
{
    private EntryRepositoryInterface $entryRepository;


    public function __construct(EntryRepositoryInterface $entryRepositoryInterface)
    {
        $this->entryRepository = $entryRepositoryInterface;
    }


    /**
     * @param array $entries
     */
    public function execute(array $entries): void
    {
        if(array_key_exists('kept', $entries))
        {
            if(count($entries['kept']) > 0)
            {
                foreach ($entries['kept'] as $entry)
                    $this->entryRepository->setDuplicityAnalysis($entry, 'kept');
            }
        }

        if(array_key_exists('excluded', $entries))
        {
            if(count($entries['excluded']) > 0)
            {
                foreach ($entries['excluded'] as $entry)
                    $this->entryRepository->setDuplicityAnalysis($entry, 'excluded');
            }
        }
    }
}
