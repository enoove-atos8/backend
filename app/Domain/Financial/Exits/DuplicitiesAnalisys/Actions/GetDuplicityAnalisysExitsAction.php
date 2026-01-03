<?php

namespace Domain\Financial\Exits\DuplicitiesAnalisys\Actions;

use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Illuminate\Support\Collection;

class GetDuplicityAnalisysExitsAction
{
    private ExitRepositoryInterface $exitRepository;


    public function __construct(ExitRepositoryInterface $exitRepositoryInterface)
    {
        $this->exitRepository = $exitRepositoryInterface;
    }

    public function execute(string $date): Collection
    {
        return $this->exitRepository->getDuplicitiesExits($date);
    }
}
