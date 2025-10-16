<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Accounts;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use App\Domain\Financial\AccountsAndCards\Accounts\Models\AccountsFiles;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountFilesRepository extends BaseRepository implements AccountFileRepositoryInterface
{
    protected mixed $model = AccountsFiles::class;

    const TABLE_NAME = 'accounts_files';
    const DELETED_COLUMN = 'deleted';

    const ID_COLUMN_JOINED = 'accounts_files.id';
    const ACCOUNT_ID_COLUMN_JOINED = 'accounts_files.account_id';
    const TO_PROCESS = 'to_process';
    const MOVEMENTS_IN_PROGRESS = 'movements_in_progress';
    const MOVEMENTS_DONE = 'movements_done';
    const MOVEMENTS_ERROR = 'movements_error';
    const CONCILIATION_IN_PROGRESS = 'conciliation_in_progress';
    const CONCILIATION_DONE = 'conciliation_done';
    const CONCILIATION_ERROR = 'conciliation_error';
    const DIFFERENT_ACCOUNT_FILE = 'different_account_file';
    const DIFFERENT_MONTH_FILE = 'different_month_file';
    const TYPE_PROCESSING_MOVEMENTS_EXTRACTION = 'movements_extraction';
    const TYPE_PROCESSING_BANK_CONCILIATION = 'bank_conciliation';
    const PDF_TYPE_EXTRACTION = 'PDF';
    const TXT_TYPE_EXTRACTION = 'TXT';
    const OFX_TYPE_EXTRACTION = 'OFX';


    const DISPLAY_SELECT_COLUMNS = [
        'accounts_files.id as accounts_files_id',
        'accounts_files.account_id as accounts_files_account_id',
        'accounts_files.original_filename as accounts_files_original_filename',
        'accounts_files.link as accounts_files_link',
        'accounts_files.file_type as accounts_files_file_type',
        'accounts_files.version as accounts_files_version',
        'accounts_files.reference_date as accounts_files_reference_date',
        'accounts_files.status as accounts_files_status',
        'accounts_files.error_message as accounts_files_error_message',
        'accounts_files.deleted as accounts_files_deleted',
    ];

    /**
     * @inheritDoc
     * @throws UnknownProperties
     */
    public function saveFile(AccountFileData $accountFileData): AccountFileData
    {
        $conditions = null;

        if($accountFileData->replaceExisting)
        {
            $conditions = [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $accountFileData->id,
            ];
        }

        $updatedOrCreated = $this->updateOrCreate([
            'account_id'            => $accountFileData->accountId,
            'original_filename'     => $accountFileData->originalFilename,
            'link'                  => $accountFileData->link,
            'file_type'             => $accountFileData->fileType,
            'version'               => $accountFileData->version,
            'reference_date'        => $accountFileData->referenceDate,
            'status'                => $accountFileData->replaceExisting && $accountFileData->id != null ? 'to_process' : $accountFileData->status,
            'error_message'         => $accountFileData->errorMessage,
            'deleted'               => $accountFileData->deleted,
        ], $conditions);

        return AccountFileData::fromSelf($updatedOrCreated->toArray());
    }


    /**
     * @param int $id
     * @param string $status
     * @return bool
     * @throws BindingResolutionException
     */
    public function changeFileProcessingStatus(int $id, string $status): bool
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,
        ];

        return $this->update($conditions, [
            'status' =>   $status,
        ]);
    }


    /**
     * Get all Files loaded to account id
     *
     * @param int $accountId
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getFilesByAccountId(int $accountId): Collection
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            AccountRepository::DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use ($accountId, $displayColumnsFromRelationship){

            $q = DB::table(self::TABLE_NAME)
                ->select($displayColumnsFromRelationship)

                ->leftJoin(AccountRepository::TABLE_NAME, self::ACCOUNT_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    AccountRepository::ID_COLUMN_JOINED)

                ->where(self::ACCOUNT_ID_COLUMN_JOINED, BaseRepository::OPERATORS['EQUALS'], $accountId)
                ->where(self::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], false)

                ->orderByDesc(self::ID_COLUMN_JOINED);


            $result = $q->get();
            return collect($result)->map(fn($item) => AccountFileData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }



    /**
     * @param int $id
     * @return \App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Files\\App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData
     * @throws BindingResolutionException
     */
    public function getFilesById(int $id): AccountFileData
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            AccountRepository::DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use ($id, $displayColumnsFromRelationship){

            $q = DB::table(self::TABLE_NAME)
                ->select($displayColumnsFromRelationship)

                ->leftJoin(AccountRepository::TABLE_NAME, self::ACCOUNT_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    AccountRepository::ID_COLUMN_JOINED)

                ->where(self::ID_COLUMN_JOINED, BaseRepository::OPERATORS['EQUALS'], $id);


            $result = $q->first();
            return AccountFileData::fromResponse((array) $result);
        };

        return $this->doQuery($query);
    }


    /**
     * Delete a file
     *
     * @param int $accountId
     * @param int $id
     * @return bool
     * @throws BindingResolutionException
     */
    public function deleteFile(int $accountId, int $id): bool
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,
        ];

        return $this->update($conditions, [
            'deleted' =>   1,
        ]);
    }
}
