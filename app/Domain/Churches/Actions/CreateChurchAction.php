<?php

namespace Domain\Churches\Actions;

use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Churches\DataTransferObjects\ChurchData;
use Domain\Churches\Models\Church;
use Domain\Churches\Models\Tenant;
use Domain\Users\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Church\ChurchRepository;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class CreateChurchAction
{
    const DOMAIN = '.atos242.local';
    private ChurchRepository $churchRepository;
    private CreateDomainGoDaddyAction $createDomainGoDaddyAction;

    public function __construct(ChurchRepositoryInterface $churchRepositoryInterface, CreateDomainGoDaddyAction $createDomainGoDaddyAction)
    {
        $this->churchRepository = $churchRepositoryInterface;
        $this->createDomainGoDaddyAction = $createDomainGoDaddyAction;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws \Throwable
     */
    public function __invoke(ChurchData $churchData): Church
    {
        // TODO: 2 - Implementar Transactions em todos as actions
        $newTenant = Tenant::create(['id' => $churchData->tenantId]);
        $newTenant->domains()->create(['domain' => $churchData->tenantId . self::DOMAIN]);

        if (is_object($newTenant))
        {
            $church = $this->churchRepository->newChurch($churchData);

            if (is_object($church))
            {
                $tenantCreated = Tenant::find($churchData->tenantId);
                tenancy()->initialize($tenantCreated);

                if($tenantCreated)
                {
                    // TODO: Colocar código de criar um usuário na action CreateUserAction

                    $user = User::create([
                        'email' => $churchData->adminEmailTenant,
                        'password' => bcrypt($churchData->passAdminEmailTenant),
                        'activated' => bcrypt($churchData->passAdminEmailTenant),
                        'type' => $churchData->activated,
                    ]);

                    if($user)
                    {
                        $goDaddyDomainCreated = $this->createDomainGoDaddyAction->__invoke($churchData->tenantId);

                        if($goDaddyDomainCreated)
                        {
                            return $church;
                        }
                    }
                    throw_if(!$user, GeneralExceptions::class, 'Não foi possível criar o usuário adminsitrador na base de dados: ' . $churchData->tenantId, 500);
                }
                throw_if(!$tenantCreated, GeneralExceptions::class, 'Não foi encontrado um tenant com esse id', 404);
            }
            throw_if(!is_object($church), GeneralExceptions::class, 'Erro ao criar uma igreja na base central', 500);
        }
    }
}
