<?php

namespace Domain\Churches\Actions;

use App\Domain\Churches\Constants\ReturnMessages;
use Domain\Churches\DataTransferObjects\ChurchData;
use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Churches\Models\Church;
use Domain\Churches\Models\Tenant;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\DataTransferObjects\UserDetailData;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Church\ChurchRepository;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
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
