<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Models\Church;
use Exception;
use Illuminate\Database\Eloquent\Model;
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
    public function execute(string $tenant): Model
    {
        try
        {
            return $this->churchRepository->getChurch($tenant);
        }
        catch (Exception)
        {
            throw new GeneralExceptions('Houve um erro ao tentar acessar o central database!', 500);
        }
    }
}
