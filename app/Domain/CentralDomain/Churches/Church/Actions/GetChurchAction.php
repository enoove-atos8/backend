<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Exception;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class GetChurchAction
{
    private ChurchRepositoryInterface $churchRepository;

    public function __construct(
        ChurchRepositoryInterface  $churchRepositoryInterface,
    )
    {
        $this->churchRepository = $churchRepositoryInterface;
    }


    /**
     * @throws Throwable
     */
    public function execute(string $tenant): ?ChurchData
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
