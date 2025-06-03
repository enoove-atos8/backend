<?php

namespace Application\Core\Jobs\Financial\Purchases;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardPurchaseData;
use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Domain\Financial\Exits\Purchases\Actions\CreateInvoiceAction;
use Domain\Financial\Exits\Purchases\Actions\CreatePurchaseAction;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardByIdAction;
use Domain\Financial\Exits\Purchases\Actions\GetInvoiceAction;
use Domain\SyncStorage\Actions\GetSyncStorageDataAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;
use Throwable;

class ProcessingPurchaseCards
{

    private GetInvoiceAction $getInvoiceAction;

    private CreatePurchaseAction $createPurchaseAction;

    private GetChurchesAction $getChurchesAction;

    private GetSyncStorageDataAction $getSyncStorageDataAction;
    private GetCardByIdAction $getCardByIdAction;
    private CreateInvoiceAction $createInvoiceAction;

    protected Collection $syncStorageData;

    public function __construct(
        GetInvoiceAction $getInvoiceAction,
        CreatePurchaseAction $createPurchaseAction,
        GetChurchesAction $getChurchesAction,
        GetSyncStorageDataAction $getSyncStorageDataAction,
        CreateInvoiceAction $createInvoiceAction,
        GetCardByIdAction $getCardByIdAction
    )
    {
        $this->createPurchaseAction = $createPurchaseAction;
        $this->getInvoiceAction = $getInvoiceAction;
        $this->getChurchesAction = $getChurchesAction;
        $this->getSyncStorageDataAction = $getSyncStorageDataAction;
        $this->createInvoiceAction = $createInvoiceAction;
        $this->getCardByIdAction = $getCardByIdAction;
    }




    /**
     * @throws GeneralExceptions
     * @throws BindingResolutionException|Throwable
     */
    public function handle(): void
    {
        $tenants = $this->getActiveTenants();
        //$tenants = $this->getTenantsByPlan(PlanRepository::PLAN_GOLD_NAME);

        foreach ($tenants as $tenant)
        {
            tenancy()->initialize($tenant);
            $this->syncStorageData = $this->getSyncStorageDataAction->execute(ExitRepository::EXITS_VALUE, SyncStorageRepository::PURCHASE_SUB_TYPE_VALUE);

            foreach ($this->syncStorageData as $data)
            {
                $purchaseData = CardPurchaseData::fromSyncStorageData($data, (int) $data->cardId);
                $this->createPurchaseAction->execute($purchaseData);
            }
        }

    }



    /**
     * @throws Throwable
     */
    public function getActiveTenants(): array
    {
        $arrTenants = [];
        $tenants = $this->getChurchesAction->execute();

        if(count($tenants) > 0)
        {
            foreach ($tenants as $tenant)
                $arrTenants[] = $tenant->tenant_id;

            return $arrTenants;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::NOT_FOUND_CHURCHES, 404);
        }
    }
}
