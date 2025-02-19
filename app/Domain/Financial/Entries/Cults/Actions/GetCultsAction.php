<?php

namespace Domain\Financial\Entries\Cults\Actions;

use App\Domain\Financial\Entries\Cults\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Cults\CultRepository;

class GetCultsAction
{
    private CultRepository $cultRepository;
    private EntryRepository $entryRepository;

    public function __construct(CultRepositoryInterface $cultRepositoryInterface, EntryRepositoryInterface $entryRepositoryInterface)
    {
        $this->cultRepository = $cultRepositoryInterface;
        $this->entryRepository = $entryRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function execute(): Collection | null
    {
        $cults = $this->cultRepository->getCults();

        if(count($cults) > 0)
        {
            foreach ($cults as $cult)
                $cult->entries = $this->entryRepository->getEntriesByCultId($cult->id);

            return $cults;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::NO_CULTS_FOUNDED, 404);
        }
    }
}
