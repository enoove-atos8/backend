<?php

namespace Domain\Mobile\SyncStorage\Actions;

use Domain\Mobile\SyncStorage\DataTransferObjects\SyncStorageData;
use Domain\Mobile\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use Domain\Mobile\SyncStorage\Models\SyncStorage;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class NewReceiptToProcessAction
{
    private SyncStorageRepository $syncStorageRepository;
    private UploadReceiptAction $uploadReceiptAction;
    private UploadFile $uploadFile;

    public function __construct(
        SyncStorageRepositoryInterface $syncStorageRepositoryInterface,
        UploadReceiptAction $uploadReceiptAction,
        UploadFile $uploadFile
    )
    {
        $this->syncStorageRepository = $syncStorageRepositoryInterface;
        $this->uploadReceiptAction = $uploadReceiptAction;
        $this->uploadFile = $uploadFile;
    }

    /**
     * @throws Throwable
     */
    public function execute(SyncStorageData $syncStorageData, $file): SyncStorage
    {
        $syncStorage = $this->syncStorageRepository->sendToDataServer($syncStorageData);

        if(!is_null($syncStorage->id))
        {
            //$this->uploadReceiptAction->execute($file, $syncStorageData->path, $syncStorageData->tenant);
            $this->uploadFile->upload($file, $syncStorageData->path, $syncStorageData->tenant);
        }
        else
            throw new GeneralExceptions('Houve um erro registrar...', 500);

        return $syncStorage;
    }
}
