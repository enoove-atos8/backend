<?php

namespace Domain\Financial\Entries\Cults\Actions;

use App\Domain\Financial\Entries\Cults\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Cults\Models\Cult;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Cults\CultRepository;

class GetDataCultByIdAction
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
    public function execute(int $id): Cult|Model
    {
        $cult = $this->cultRepository->getCultById($id);
        $cult->entries = $this->entryRepository->getEntriesByCultId($cult->id);

        if($cult->id != null)
        {
            return $cult;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::NO_CULTS_FOUNDED, 404);
        }
    }
}
