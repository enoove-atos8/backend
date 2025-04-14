<?php

namespace Domain\Financial\Exits\Exits\Actions;

use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Throwable;

class GetExitByTimestampAction
{
    private ExitRepository $exitRepository;

    public function __construct(
        ExitRepositoryInterface $exitRepositoryInterface,
    )
    {
        $this->exitRepository = $exitRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(string $timestamp): Model | null
    {
        $entry = $this->exitRepository->getExitByTimestamp($timestamp);

        if($entry != null)
        {
            return $entry;
        }
        else
        {
            return null;
        }
    }
}
