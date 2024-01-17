<?php

namespace Domain\Churches\Actions;

use App\Domain\Churches\Constants\ReturnMessages;
use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Churches\DataTransferObjects\ChurchData;
use Domain\Churches\Models\Church;
use Domain\Churches\Models\Tenant;
use Domain\Users\Actions\CreateUserAction;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\DataTransferObjects\UserDetailData;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Church\ChurchRepository;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class CreateChurchAction
{
    const DOMAIN = '.atos8.com';
    private ChurchRepository $churchRepository;
    private CreateDomainGoDaddyAction $createDomainGoDaddyAction;
    private CreateUserAction $createUserAction;

    public function __construct(
        ChurchRepositoryInterface  $churchRepositoryInterface,
        CreateDomainGoDaddyAction $createDomainGoDaddyAction,
        CreateUserAction $createUserAction,
    )
    {
        $this->churchRepository = $churchRepositoryInterface;
        $this->createDomainGoDaddyAction = $createDomainGoDaddyAction;
        $this->createUserAction = $createUserAction;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Throwable
     */
    public function __invoke(ChurchData $churchData, UserData $userData, UserDetailData $userDetailData): array
    {
        $awsS3Bucket = config('aws.environments.' . App::environment() . '.s3' ) . $churchData->tenantId;
        $domain = config('domain.' . App::environment());

        $newTenant = Tenant::create(['id' => $churchData->tenantId]);
        $newTenant->domains()->create(['domain' => $churchData->tenantId . '.' . $domain]);

        Artisan::call('tenants:seed', ['--tenants' => [$churchData->tenantId],]);


        if (is_object($newTenant))
        {
            $church = $this->churchRepository->newChurch($churchData, $awsS3Bucket);

            $tenantCreated = Tenant::find($churchData->tenantId);
            tenancy()->initialize($tenantCreated);

            if($tenantCreated)
            {
                $user = $this->createUserAction->__invoke($userData, $userDetailData, $churchData->tenantId, true);
                $goDaddyDomainCreated = $this->createDomainGoDaddyAction->__invoke($churchData->tenantId);

                if($goDaddyDomainCreated)
                {
                    return [
                        'message'   =>  ReturnMessages::SUCCESS_CHURCH_REGISTERED,
                        'data'      =>  [
                            'church'    =>  $church,
                            'user'      =>  $user
                        ]
                    ];
                }
            }
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_TENANT, 500);
        }
    }
}
