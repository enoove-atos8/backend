<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use App\Domain\Accounts\Users\Actions\CreateUserAction;
use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Domain\CentralDomain\Churches\Church\Constants\S3DefaultFolders;
use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Models\Tenant;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Util\Storage\S3\ConnectS3;
use Infrastructure\Util\Storage\S3\CreateDirectory;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class CreateChurchAction
{
    private ChurchRepository $churchRepository;
    private CreateSubDomainAction $createSubDomainAction;
    private CreateUserAction $createUserAction;
    private CreateDirectory $createDirectory;
    private CreateS3DefaultFoldersAction $createS3DefaultFoldersAction;

    private ConnectS3 $s3;

    public function __construct(
        ChurchRepositoryInterface       $churchRepositoryInterface,
        CreateSubDomainAction           $createSubDomainAction,
        CreateUserAction                $createUserAction,
        ConnectS3                       $connectS3,
        CreateS3DefaultFoldersAction    $createS3DefaultFoldersAction,
    )
    {
        $this->churchRepository = $churchRepositoryInterface;
        $this->createSubDomainAction = $createSubDomainAction;
        $this->createUserAction = $createUserAction;
        $this->s3 = $connectS3;
        $this->createS3DefaultFoldersAction = $createS3DefaultFoldersAction;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Throwable
     */
    public function execute(ChurchData $churchData, UserData $userData, UserDetailData $userDetailData): array
    {
        $env = App::environment();
        $s3Bucket = config('services-host.services.s3.environments.' . $env . '.S3_ENDPOINT_EXTERNAL_ACCESS' ) . '/' . $churchData->tenantId;
        $domain = config('services-hosts.environments.' . $env . '.domain');
        $s3 = $this->s3->getInstance();

        $newTenant = Tenant::create(['id' => $churchData->tenantId]);
        $newTenant->domains()->create(['domain' => $churchData->tenantId . '.' . $domain]);

        Artisan::call('tenants:seed', ['--tenants' => [$churchData->tenantId],]);


        if (is_object($newTenant))
        {
            $church = $this->churchRepository->newChurch($churchData, $s3Bucket);

            $tenantCreated = Tenant::find($churchData->tenantId);
            tenancy()->initialize($tenantCreated);

            if($tenantCreated)
            {
                $user = $this->createUserAction->execute($userData, $userDetailData, $churchData->tenantId, true);
                $subDomainActionCreated = $this->createSubDomainAction->execute($churchData->tenantId);

                if($subDomainActionCreated)
                {
                    if(!$s3->doesBucketExist($churchData->tenantId))
                    {
                        $s3->createBucket(['Bucket' => $churchData->tenantId,]);
                        $this->s3->setBucketAsPublic($churchData->tenantId, $s3);

                        $this->createS3DefaultFoldersAction->execute(S3DefaultFolders::S3_DEFAULT_FOLDERS, $churchData->tenantId);

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
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_TENANT, 500);
        }
    }
}
