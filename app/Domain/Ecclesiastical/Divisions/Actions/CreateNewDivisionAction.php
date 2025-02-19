<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Domain\Ecclesiastical\Divisions\Constants\ReturnMessages;
use Domain\Financial\SyncStorage\Actions\AddPathSyncStorageAction;
use Domain\Financial\SyncStorage\Actions\GetSyncStorageDataByPathAction;
use Domain\Financial\SyncStorage\DataTransferObjects\SyncStorageData;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Infrastructure\Util\Storage\S3\CreateDirectory;
use Throwable;

class CreateNewDivisionAction
{
    private DivisionRepository $divisionRepository;

    private AddPathSyncStorageAction $addPathSyncStorageAction;
    private CreateDirectory $createDirectory;

    private GetSyncStorageDataByPathAction $getSyncStorageDataByPathAction;

    const BASE_PATH_DESIGNATED_ENTRIES_SHARED_RECEIPTS = 'sync_storage/financial/entries/shared_receipts/designated';

    public function __construct(
        DivisionRepositoryInterface $divisionRepository,
        CreateDirectory $createDirectory,
        AddPathSyncStorageAction $addPathSyncStorageAction,
        GetSyncStorageDataByPathAction $getSyncStorageDataByPathAction
    )
    {
        $this->divisionRepository = $divisionRepository;
        $this->createDirectory = $createDirectory;
        $this->addPathSyncStorageAction = $addPathSyncStorageAction;
        $this->getSyncStorageDataByPathAction = $getSyncStorageDataByPathAction;
    }



    /**
     * @throws Throwable
     */
    public function execute(DivisionData $divisionData, $tenant): Division
    {
        $existDivision = $this->divisionRepository->getDivisionByName($divisionData->slug);

        if(is_null($existDivision))
        {
            $division = $this->divisionRepository->createDivision($divisionData);
            $this->createDirectory->createDirectory(self::BASE_PATH_DESIGNATED_ENTRIES_SHARED_RECEIPTS . '/'. $divisionData->slug, $tenant);

            if(!is_null($division->id))
            {
                return $division;
            }
            else
            {
                throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_DIVISION, 500);
            }
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_ALREADY_DIVISION, 500);
        }
    }
}
