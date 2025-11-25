<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use App\Domain\Accounts\Users\Actions\CreateUserAction;
use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\CentralDomain\Billing\Actions\CreateSubscriptionAction;
use App\Domain\CentralDomain\Billing\Actions\SaveSubscriptionAction;
use App\Infrastructure\Repositories\CentralDomain\Church\ChurchRepository;
use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Models\Tenant;
use Domain\CentralDomain\PaymentGateway\Actions\CreateStripeCustomerAction;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Infrastructure\Exceptions\GeneralExceptions;
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

    private CreateStripeCustomerAction $createStripeCustomerAction;

    private CreateSubscriptionAction $createSubscriptionAction;

    private SaveSubscriptionAction $saveSubscriptionAction;

    private ConnectS3 $s3;

    public function __construct(
        ChurchRepositoryInterface $churchRepositoryInterface,
        CreateSubDomainAction $createSubDomainAction,
        CreateUserAction $createUserAction,
        ConnectS3 $connectS3,
        CreateS3DefaultFoldersAction $createS3DefaultFoldersAction,
        CreateStripeCustomerAction $createStripeCustomerAction,
        CreateSubscriptionAction $createSubscriptionAction,
        SaveSubscriptionAction $saveSubscriptionAction,
    ) {
        $this->churchRepository = $churchRepositoryInterface;
        $this->createSubDomainAction = $createSubDomainAction;
        $this->createUserAction = $createUserAction;
        $this->s3 = $connectS3;
        $this->createS3DefaultFoldersAction = $createS3DefaultFoldersAction;
        $this->createStripeCustomerAction = $createStripeCustomerAction;
        $this->createSubscriptionAction = $createSubscriptionAction;
        $this->saveSubscriptionAction = $saveSubscriptionAction;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Throwable
     */
    public function execute(ChurchData $churchData, UserData $userData, UserDetailData $userDetailData): array
    {
        $env = App::environment();
        $s3Bucket = config('services-host.services.s3.environments.'.$env.'.S3_ENDPOINT_EXTERNAL_ACCESS').'/'.$churchData->tenantId;
        $domain = config('services-hosts.environments.'.$env.'.domain');
        $s3 = $this->s3->getInstance();

        $newTenant = null;
        $church = null;
        $stripeCustomerId = null;
        $s3BucketCreated = false;

        try {
            $newTenant = Tenant::create(['id' => $churchData->tenantId]);
            $newTenant->domains()->create(['domain' => $churchData->tenantId.'.'.$domain]);

            Artisan::call('tenants:seed', ['--tenants' => [$churchData->tenantId]]);

            $church = $this->churchRepository->newChurch($churchData, $s3Bucket);

            $tenantCreated = Tenant::find($churchData->tenantId);
            tenancy()->initialize($tenantCreated);

            $user = $this->createUserAction->execute($userData, $userDetailData, $churchData->tenantId, true);

            $subDomainActionCreated = $this->createSubDomainAction->execute($churchData->tenantId);
            if (! $subDomainActionCreated) {
                throw new GeneralExceptions('Erro ao criar subdomínio', 500);
            }

            if (! $s3->doesBucketExist($churchData->tenantId)) {
                $s3->createBucket(['Bucket' => $churchData->tenantId]);
                $this->s3->setBucketAsPublic($churchData->tenantId, $s3);
                $s3BucketCreated = true;
            }

            $stripeCustomerId = $this->createStripeCustomerAction->execute($churchData);
            if ($stripeCustomerId) {
                $churchData->stripeId = $stripeCustomerId;
                $church->stripe_id = $stripeCustomerId;
                $church->save();
            }

            $subscriptionResult = null;
            if ($stripeCustomerId && $churchData->paymentMethodId) {
                try {
                    $subscriptionResult = $this->createSubscriptionAction->execute(
                        $stripeCustomerId,
                        $churchData->paymentMethodId,
                        $churchData->planId,
                        $churchData->memberCount
                    );
                } catch (\Exception $e) {
                    $subscriptionResult = null;
                }
            }

            if ($subscriptionResult && $church) {
                try {
                    $this->saveSubscriptionAction->execute($church->id, $subscriptionResult);
                } catch (\Exception $e) {
                }
            }

            return [
                'message' => ReturnMessages::SUCCESS_CHURCH_REGISTERED,
                'data' => [
                    'church' => $church,
                    'user' => $user,
                ],
            ];
        } catch (\Exception $e) {
            $this->rollbackChurchCreation($newTenant, $church, $s3BucketCreated, $churchData->tenantId, $s3);

            throw new GeneralExceptions(
                'Erro ao criar igreja: '.$e->getMessage(),
                $e->getCode() ?: 500,
                $e
            );
        }
    }

    private function rollbackChurchCreation(
        ?Tenant $tenant,
        $church,
        bool $s3BucketCreated,
        string $tenantId,
        $s3
    ): void {
        try {
            if ($s3BucketCreated) {
                try {
                    if ($s3->doesBucketExist($tenantId)) {
                        $s3->deleteBucket(['Bucket' => $tenantId]);
                    }
                } catch (\Exception $e) {
                }
            }

            if ($church && $church->id) {
                try {
                    tenancy()->central(function () use ($church) {
                        $church->delete();
                    });
                } catch (\Exception $e) {
                }
            }

            if ($tenant) {
                try {
                    $tenant->delete();
                } catch (\Exception $e) {
                }
            }
        } catch (\Exception $e) {
        }
    }
}
