<?php

namespace Domain\Financial\Exits\DuplicitiesAnalisys\Actions;

use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

class GetReceiptsByExitsIdsAction
{
    private ExitRepositoryInterface $exitRepository;


    public function __construct(ExitRepositoryInterface $exitRepositoryInterface)
    {
        $this->exitRepository = $exitRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     */
    public function execute(array $ids): Collection
    {
        return $this->exitRepository->getReceiptsExitsByIds($ids);
    }
}
