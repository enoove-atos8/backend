<?php

namespace Domain\Financial\Entries\Cults\Actions;

use App\Domain\Financial\Entries\Cults\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Cults\CultRepository;

class GetCultsAction
{
    private CultRepository $cultRepository;

    public function __construct(CultRepositoryInterface $cultRepositoryInterface)
    {
        $this->cultRepository = $cultRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke(): Collection | null
    {
        $cults = $this->cultRepository->getCults();

        if(count($cults) > 0)
        {
            return $cults;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::NO_CULTS_FOUNDED, 404);
        }
    }
}
