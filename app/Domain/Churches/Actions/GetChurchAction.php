<?php

namespace Domain\Churches\Actions;

use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Churches\Models\Church;
use Exception;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Church\ChurchRepository;
use Throwable;

class GetChurchAction
{
    private ChurchRepository $churchRepository;

    public function __construct(
        ChurchRepositoryInterface  $churchRepositoryInterface,
    )
    {
        $this->churchRepository = $churchRepositoryInterface;
    }


    /**
     * @throws Throwable
     */
    public function __invoke(string $tenant): Church
    {
        try
        {
            return tenancy()->central(function () use ($tenant){
                 return $this->churchRepository->getChurch($tenant);
            });
        }
        catch (Exception)
        {
            throw new GeneralExceptions('Houve um erro ao tentar acessar o central database!', 500);
        }
    }
}
