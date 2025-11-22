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

        // Variáveis de controle para rollback
        $newTenant = null;
        $church = null;
        $stripeCustomerId = null;
        $s3BucketCreated = false;

        try {
            // 1. Criar Tenant (cria banco de dados físico + registro em 'tenants')
            $newTenant = Tenant::create(['id' => $churchData->tenantId]);
            $newTenant->domains()->create(['domain' => $churchData->tenantId.'.'.$domain]);

            // 2. Executar seeds no banco do tenant
            Artisan::call('tenants:seed', ['--tenants' => [$churchData->tenantId]]);

            // 3. Criar Stripe Customer
            $stripeCustomerId = $this->createStripeCustomerAction->execute($churchData);
            if ($stripeCustomerId) {
                $churchData->stripeId = $stripeCustomerId;
            }

            // 4. Processar subscription se payment_method_id foi fornecido
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
                    // Log error mas não falha criação da igreja
                    // A subscription pode ser criada depois
                    $subscriptionResult = null;
                }
            }

            // 5. Criar registro da Church no banco central (tabela 'churches')
            $church = $this->churchRepository->newChurch($churchData, $s3Bucket);

            // 6. Inicializar contexto do tenant
            $tenantCreated = Tenant::find($churchData->tenantId);
            tenancy()->initialize($tenantCreated);

            // 7. Criar usuário admin no banco do tenant
            $user = $this->createUserAction->execute($userData, $userDetailData, $churchData->tenantId, true);

            // 8. Criar subdomínio
            $subDomainActionCreated = $this->createSubDomainAction->execute($churchData->tenantId);
            if (! $subDomainActionCreated) {
                throw new GeneralExceptions('Erro ao criar subdomínio', 500);
            }

            // 9. Salvar subscription localmente se foi criada
            if ($subscriptionResult && $church) {
                try {
                    $this->saveSubscriptionAction->execute($church->id, $subscriptionResult);
                } catch (\Exception $e) {
                    // Log error mas não falha criação da igreja
                }
            }

            // 10. Criar bucket S3
            if (! $s3->doesBucketExist($churchData->tenantId)) {
                $s3->createBucket(['Bucket' => $churchData->tenantId]);
                $this->s3->setBucketAsPublic($churchData->tenantId, $s3);
                $s3BucketCreated = true;

                // $this->createS3DefaultFoldersAction->execute(S3DefaultFolders::S3_DEFAULT_FOLDERS, $churchData->tenantId);
            }

            // Sucesso - retornar resultado
            return [
                'message' => ReturnMessages::SUCCESS_CHURCH_REGISTERED,
                'data' => [
                    'church' => $church,
                    'user' => $user,
                ],
            ];
        } catch (\Exception $e) {
            // ROLLBACK: Reverter todas as operações realizadas
            $this->rollbackChurchCreation($newTenant, $church, $s3BucketCreated, $churchData->tenantId, $s3);

            // Re-lançar a exceção original
            throw new GeneralExceptions(
                'Erro ao criar igreja: '.$e->getMessage(),
                $e->getCode() ?: 500,
                $e
            );
        }
    }

    /**
     * Reverte todas as operações realizadas durante a criação da igreja
     *
     * @param  \Domain\CentralDomain\Churches\Church\Models\Church|null  $church
     * @param  mixed  $s3
     */
    private function rollbackChurchCreation(
        ?Tenant $tenant,
        $church,
        bool $s3BucketCreated,
        string $tenantId,
        $s3
    ): void {
        try {
            // 1. Deletar bucket S3 se foi criado
            if ($s3BucketCreated) {
                try {
                    if ($s3->doesBucketExist($tenantId)) {
                        $s3->deleteBucket(['Bucket' => $tenantId]);
                    }
                } catch (\Exception $e) {
                    // Log error but continue rollback
                }
            }

            // 2. Deletar registro da tabela 'churches' no banco central
            if ($church && $church->id) {
                try {
                    tenancy()->central(function () use ($church) {
                        $church->delete();
                    });
                } catch (\Exception $e) {
                    // Log error but continue rollback
                }
            }

            // 3. Deletar Tenant (deleta registro em 'tenants', 'domains' e o banco de dados físico)
            if ($tenant) {
                try {
                    // O método delete() do Tenant faz:
                    // - Deleta registros em 'domains' (cascade)
                    // - Deleta registro em 'tenants'
                    // - Deleta o banco de dados físico (db_tenantId)
                    $tenant->delete();
                } catch (\Exception $e) {
                    // Log error but continue
                }
            }

            // 4. Deletar Stripe Customer/Subscription se foi criado
            // Nota: Stripe não permite deletar customers facilmente,
            // então apenas deixamos órfão (pode ser limpo manualmente depois)
        } catch (\Exception $e) {
            // Log rollback errors but don't throw
            // O importante é que tentamos limpar
        }
    }
}
