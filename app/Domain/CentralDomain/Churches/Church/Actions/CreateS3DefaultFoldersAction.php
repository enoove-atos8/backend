<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Domain\Ecclesiastical\Folders\DataTransferObjects\SyncFoldersData;
use Domain\Financial\SyncStorage\Actions\AddPathSyncStorageAction;
use Domain\Financial\SyncStorage\Actions\GetSyncStorageDataByPathAction;
use Domain\Financial\SyncStorage\DataTransferObjects\SyncStorageData;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\ConnectS3;
use Infrastructure\Util\Storage\S3\CreateDirectory;

class CreateS3DefaultFoldersAction
{

    private ConnectS3 $s3;
    private SyncStorageData $syncStorageData;
    private CreateDirectory $createDirectory;

    private GetSyncStorageDataByPathAction $getSyncStorageDataByPathAction;

    private AddPathSyncStorageAction $addPathSyncStorageAction;
    private string $tenant;

    public function __construct(
        ConnectS3 $connectS3,
        SyncStorageData $syncStorageData,
        CreateDirectory $createDirectory,
        AddPathSyncStorageAction $addPathSyncStorageAction,
        GetSyncStorageDataByPathAction $getSyncStorageDataByPathAction,
    )
    {
        $this->s3 = $connectS3;
        $this->syncStorageData = $syncStorageData;
        $this->createDirectory = $createDirectory;
        $this->addPathSyncStorageAction = $addPathSyncStorageAction;
        $this->getSyncStorageDataByPathAction = $getSyncStorageDataByPathAction;
    }

    /**
     * @throws GeneralExceptions
     */
    public function execute(array $folders, string $tenant): void
    {
        $this->syncStorageData->tenant = $tenant;
        $this->createAndPersistDirectoriesPath($folders, $this->s3->getInstance());
    }


    /**
     * @throws GeneralExceptions
     */
    public function createAndPersistDirectoriesPath(array $folders, S3Client $s3, string $basePath = ''): void
    {
        $tenant = $this->syncStorageData->tenant;

        foreach ($folders as $folder => $subFolders) {

            $currentFolder = is_numeric($folder) ? $subFolders : $folder;
            $currentPath = rtrim($basePath, '/') . '/' . $currentFolder . '/';

            $this->createDirectory->createDirectory($currentPath, $tenant);

            if (is_array($subFolders) && !empty($subFolders))
            {
                $this->createAndPersistDirectoriesPath($subFolders, $s3, $currentPath);
            }
            else
            {
                $this->syncStorageData = $this->getSyncStorageDataByPathAction->execute($currentPath);
                $this->addPathSyncStorageAction->execute($this->syncStorageData, $tenant);
            }
        }
    }
}
