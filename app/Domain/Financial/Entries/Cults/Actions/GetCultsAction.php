<?php

namespace Domain\Financial\Entries\Cults\Actions;

use App\Domain\Financial\Entries\Cults\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Cults\CultRepository;

class GetCultsAction
{
    private CultRepositoryInterface $cultRepository;
    private EntryRepositoryInterface $entryRepository;

    public function __construct(CultRepositoryInterface $cultRepositoryInterface, EntryRepositoryInterface $entryRepositoryInterface)
    {
        $this->cultRepository = $cultRepositoryInterface;
        $this->entryRepository = $entryRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions
     */
    public function execute(bool $paginate = true, ?string $dates = null): Collection | Paginator
    {
        $cults = $this->cultRepository->getCults($paginate, $dates);

        if(count($cults) > 0)
        {
            foreach ($cults as $cult)
                $cult->entries = $this->entryRepository->getEntriesByCultId($cult->cults_id);

            return $cults;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::NO_CULTS_FOUNDED, 404);
        }
    }
}
