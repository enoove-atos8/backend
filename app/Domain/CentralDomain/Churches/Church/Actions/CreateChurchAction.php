<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use App\Domain\Accounts\Users\Actions\CreateUserAction;
use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Models\Tenant;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Util\Storage\S3\ConnectS3;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class CreateChurchAction
{
    private ChurchRepository $churchRepository;
    private CreateSubDomainAction $createSubDomainAction;
    private CreateUserAction $createUserAction;

    private ConnectS3 $s3;

    public function __construct(
        ChurchRepositoryInterface $churchRepositoryInterface,
        CreateSubDomainAction     $createSubDomainAction,
        CreateUserAction          $createUserAction,
        ConnectS3                 $connectS3
    )
    {
        $this->churchRepository = $churchRepositoryInterface;
        $this->createSubDomainAction = $createSubDomainAction;
        $this->createUserAction = $createUserAction;
        $this->s3 = $connectS3;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Throwable
     */
    public function __invoke(ChurchData $churchData, UserData $userData, UserDetailData $userDetailData): array
    {
        $env = App::environment();
        $s3Bucket = config('services-host.services.s3.environments.' . $env . '.S3_ENDPOINT_EXTERNAL_ACCESS' ) . '/' . $churchData->tenantId;
        $domain = config('services-hosts.environments.' . $env . '.domain');
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
            $church = $this->churchRepository->newChurch($churchData, $s3Bucket);

            $tenantCreated = Tenant::find($churchData->tenantId);
            tenancy()->initialize($tenantCreated);

            if($tenantCreated)
            {
                $user = $this->createUserAction->__invoke($userData, $userDetailData, $churchData->tenantId, true);
                $subDomainActionCreated = $this->createSubDomainAction->__invoke($churchData->tenantId);

                if($subDomainActionCreated)
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
