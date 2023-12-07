<?php

namespace Domain\Churches\Actions;

use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Churches\DataTransferObjects\ChurchData;
use Domain\Churches\Models\Church;
use Domain\Churches\Models\Tenant;
use Domain\Users\Actions\CreateMemberAction;
use Domain\Users\DataTransferObjects\MemberData;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Church\ChurchRepository;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class CreateChurchAction
{
    const DOMAIN = '.atos242.com';
    private ChurchRepository $churchRepository;
    private CreateDomainGoDaddyAction $createDomainGoDaddyAction;
    private CreateMemberAction $createUserAction;

    public function __construct(
        ChurchRepositoryInterface  $churchRepositoryInterface,
        CreateDomainGoDaddyAction $createDomainGoDaddyAction,
        CreateMemberAction $createUserAction
    )
    {
        $this->churchRepository = $churchRepositoryInterface;
        $this->createDomainGoDaddyAction = $createDomainGoDaddyAction;
        $this->createUserAction = $createUserAction;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws \Throwable
     */
    public function __invoke(ChurchData $churchData, MemberData $userData): array
    {
        $domain = config('api-resources.app.domain.local');
        $newTenant = Tenant::create(['id' => $churchData->tenantId]);
        $newTenant->domains()->create(['domain' => $churchData->tenantId . '.' . $domain]);

        if (is_object($newTenant))
        {
            $church = $this->churchRepository->newChurch($churchData);

            if (is_object($church))
            {
                $tenantCreated = Tenant::find($churchData->tenantId);
                tenancy()->initialize($tenantCreated);

                if($tenantCreated)
                {
                    $user = $this->createUserAction->__invoke($userData);

                    if($user)
                    {
                        $goDaddyDomainCreated = $this->createDomainGoDaddyAction->__invoke($churchData->tenantId);

                        if($goDaddyDomainCreated)
                        {
                            return [$church, $user];
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
