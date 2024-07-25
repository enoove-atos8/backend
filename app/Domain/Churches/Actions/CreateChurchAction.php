<?php

namespace Domain\Churches\Actions;

use App\Domain\Accounts\Users\Actions\CreateUserAction;
use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Churches\Constants\ReturnMessages;
use Domain\Churches\DataTransferObjects\ChurchData;
use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Churches\Models\Tenant;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Util\Storage\S3\ConnectS3;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class CreateChurchAction
{
    const DOMAIN = '.atos8.com';
    private ChurchRepository $churchRepository;
    private CreateDomainGoDaddyAction $createDomainGoDaddyAction;
    private CreateUserAction $createUserAction;

    private ConnectS3 $s3;

    public function __construct(
        ChurchRepositoryInterface  $churchRepositoryInterface,
        CreateDomainGoDaddyAction $createDomainGoDaddyAction,
        CreateUserAction $createUserAction,
        ConnectS3 $connectS3
    )
    {
        $this->churchRepository = $churchRepositoryInterface;
        $this->createDomainGoDaddyAction = $createDomainGoDaddyAction;
        $this->createUserAction = $createUserAction;
        $this->s3 = $connectS3;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Throwable
     */
    public function __invoke(ChurchData $churchData, UserData $userData, UserDetailData $userDetailData): array
    {
        $awsS3Bucket = config('s3.environments.' . App::environment() . '.S3_ENDPOINT_EXTERNAL_ACCESS' ) . '/' . $churchData->tenantId;
        $domain = config('domain.' . App::environment());
        $s3 = $this->s3->getInstance();

        $newTenant = Tenant::create(['id' => $churchData->tenantId]);
        $newTenant->domains()->create(['domain' => $churchData->tenantId . '.' . $domain]);

        if(!$s3->doesBucketExist($churchData->tenantId))
        {
            $s3->createBucket(['Bucket' => $churchData->tenantId,]);
            $this->s3->setBucketAsPublic($churchData->tenantId, $s3);
        }

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
