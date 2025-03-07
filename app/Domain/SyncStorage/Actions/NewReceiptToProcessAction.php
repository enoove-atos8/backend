<?php

namespace App\Domain\SyncStorage\Actions;

use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Domain\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use App\Domain\SyncStorage\Models\SyncStorage;
use Domain\SyncStorage\Actions\UpdatePathWithFileNameAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class NewReceiptToProcessAction
{
    private SyncStorageRepository $syncStorageRepository;
    private UploadReceiptAction $uploadReceiptAction;
    private UploadFile $uploadFile;

    private MinioStorageService $minioStorageService;

    private UpdatePathWithFileNameAction $updatePathWithFileNameAction;

    public function __construct(
        SyncStorageRepositoryInterface $syncStorageRepositoryInterface,
        UploadReceiptAction $uploadReceiptAction,
        UploadFile $uploadFile,
        MinioStorageService $minioStorageService,
        UpdatePathWithFileNameAction $updatePathWithFileNameAction
    )
    {
        $this->syncStorageRepository = $syncStorageRepositoryInterface;
        $this->uploadReceiptAction = $uploadReceiptAction;
        $this->uploadFile = $uploadFile;
        $this->minioStorageService = $minioStorageService;
        $this->updatePathWithFileNameAction = $updatePathWithFileNameAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(SyncStorageData $syncStorageData, $file): SyncStorage
    {
        $syncStorage = $this->syncStorageRepository->sendToDataServer($syncStorageData);

        if(!is_null($syncStorage->id))
        {
            $url = $this->minioStorageService->upload($file, $syncStorageData->path, $syncStorageData->tenant);

            $urlParts = explode('/', $url);
            $fileName = end($urlParts);
            $this->updatePathWithFileNameAction->execute($syncStorage->id, $syncStorageData->path . '/' . $fileName);
        }
        else
            throw new GeneralExceptions('Houve um erro registrar...', 500);

        return $syncStorage;
    }
}
