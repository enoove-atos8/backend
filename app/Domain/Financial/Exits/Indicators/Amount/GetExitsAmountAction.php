<?php

namespace Domain\Financial\Exits\Indicators\Amount;

use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

class GetExitsAmountAction
{
    private ExitRepository $exitRepository;

    public function __construct(ExitRepositoryInterface $exitRepositoryInterface)
    {
        $this->exitRepository = $exitRepositoryInterface;
    }


    /**
     * @param $dates
     * @param $filters
     * @return array
     * @throws BindingResolutionException
     */
    public function execute($dates, $filters): array
    {
        $exits = $this->exitRepository->getExits($dates, $filters, ExitRepository::COMPENSATED_VALUE, false, true);

        return [
            'qtdExits'      =>  $exits->count(),
            'amountExits'   =>  $exits->sum(ExitRepository::AMOUNT_COLUMN),
        ];
    }
}
