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
use Infrastructure\Services\External\minIO\MinioStorageService;
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

    private MinioStorageService $minioStorageService;

    protected Collection $syncStorageData;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';
    const SHARED_RECEIPTS_FOLDER_NAME = 'shared_receipts';
    const STORED_RECEIPTS_FOLDER_NAME = 'stored_receipts';

    public function __construct(
        GetInvoiceAction $getInvoiceAction,
        CreatePurchaseAction $createPurchaseAction,
        GetChurchesAction $getChurchesAction,
        GetSyncStorageDataAction $getSyncStorageDataAction,
        CreateInvoiceAction $createInvoiceAction,
        GetCardByIdAction $getCardByIdAction,
        UpdateStatusAction $updateStatusAction,
        MinioStorageService $minioStorageService,
    )
    {
        $this->createPurchaseAction = $createPurchaseAction;
        $this->getInvoiceAction = $getInvoiceAction;
        $this->getChurchesAction = $getChurchesAction;
        $this->getSyncStorageDataAction = $getSyncStorageDataAction;
        $this->createInvoiceAction = $createInvoiceAction;
        $this->getCardByIdAction = $getCardByIdAction;
        $this->updateStatusAction = $updateStatusAction;
        $this->minioStorageService = $minioStorageService;
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
                $data->path = $this->uploadReceipt($data, $tenant);

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


    /**
     * @param mixed $data
     * @param string $tenant
     * @return string
     * @throws GeneralExceptions
     */
    public function uploadReceipt(mixed $data, string $tenant): string
    {
        $basePathTemp = self::STORAGE_BASE_PATH . "tenants/{$tenant}/temp";
        $this->minioStorageService->deleteFilesInLocalDirectory($basePathTemp);
        $downloadedFile = $this->minioStorageService->downloadFile($data->path, $tenant, $basePathTemp);

        $sharedPath = $data->path;
        $data->path = str_replace(self::SHARED_RECEIPTS_FOLDER_NAME, self::STORED_RECEIPTS_FOLDER_NAME, $data->path);
        $urlParts = explode('/', $data->path);
        array_pop($urlParts);
        $path = implode('/', $urlParts);
        $fileUrl = $this->minioStorageService->upload($downloadedFile['fileUploaded'], $path, $tenant);

        if(!empty($fileUrl))
            $this->minioStorageService->delete($sharedPath, $tenant);

        return $fileUrl;
    }
}
