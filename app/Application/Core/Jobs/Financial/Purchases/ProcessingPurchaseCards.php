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
use Domain\SyncStorage\Actions\UpdateStatusAction;
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

    private UpdateStatusAction $updateStatusAction;

    protected Collection $syncStorageData;

    public function __construct(
        GetInvoiceAction $getInvoiceAction,
        CreatePurchaseAction $createPurchaseAction,
        GetChurchesAction $getChurchesAction,
        GetSyncStorageDataAction $getSyncStorageDataAction,
        CreateInvoiceAction $createInvoiceAction,
        GetCardByIdAction $getCardByIdAction,
        UpdateStatusAction $updateStatusAction
    )
    {
        $this->createPurchaseAction = $createPurchaseAction;
        $this->getInvoiceAction = $getInvoiceAction;
        $this->getChurchesAction = $getChurchesAction;
        $this->getSyncStorageDataAction = $getSyncStorageDataAction;
        $this->createInvoiceAction = $createInvoiceAction;
        $this->getCardByIdAction = $getCardByIdAction;
        $this->updateStatusAction = $updateStatusAction;
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
            $this->syncStorageData = $this->getSyncStorageDataAction->execute(
                                ExitRepository::EXITS_VALUE,
                            SyncStorageRepository::PURCHASE_SUB_TYPE_VALUE);

            foreach ($this->syncStorageData as $data)
            {
                $purchaseData = CardPurchaseData::fromSyncStorageData($data, (int) $data->cardId);
                $purchase = $this->createPurchaseAction->execute($purchaseData);

                if(!is_null($purchase->id))
                    $this->updateStatusAction->execute($data->id, SyncStorageRepository::DONE_VALUE);
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
        }

        return $arrTenants;
    }
}
