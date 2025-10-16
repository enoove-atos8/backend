<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountFileData extends DataTransferObject
{
    /** @var int|null */
    public ?int $id;

    /** @var integer|null */
    public ?int $accountId;

    /** @var string|null */
    public ?string $originalFilename;

    /** @var string|null */
    public ?string $link;

    /** @var string|null */
    public ?string $fileType;

    /** @var integer|null */
    public ?int $version;

    /** @var string|null */
    public ?string $referenceDate;

    /** @var string|null */
    public ?string $status;

    /** @var string|null */
    public ?string $errorMessage;

    /** @var boolean|null */
    public ?bool $deleted;


    /** @var boolean|null */
    public ?bool $replaceExisting;


    /** @var AccountData | null  */
    public ?AccountData $account;


    /**
     * Create an AccountFileData instance from an array response.
     *
     * @param array $data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['accounts_files_id'] ?? null,
            accountId: $data['accounts_files_account_id'] ?? null,
            originalFilename: $data['accounts_files_original_filename'] ?? null,
            link: $data['accounts_files_link'] ?? null,
            fileType: $data['accounts_files_file_type'] ?? null,
            version: $data['accounts_files_version'] ?? null,
            referenceDate: $data['accounts_files_reference_date'] ?? null,
            status: $data['accounts_files_status'] ?? null,
            errorMessage: $data['accounts_files_error_message'] ?? null,
            deleted: $data['accounts_files_deleted'] ?? null,

            account: new AccountData(
                id: $data['accounts_id'] ?? null,
                accountType: $data['accounts_account_type'] ?? null,
                bankName: $data['accounts_bank_name'] ?? null,
                agencyNumber: $data['accounts_agency_number'] ?? null,
                accountNumber: $data['accounts_account_number'] ?? null,
            ),
        );
    }




    /**
     * Create an AccountFileData instance from an Eloquent model or array.
     * Useful for single record conversion.
     *
     * @param array $data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromSelf(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'accountId' => $data['account_id'] ?? null,
            'originalFilename' => $data['original_filename'] ?? null,
            'link' => $data['link'] ?? null,
            'fileType' => $data['file_type'] ?? null,
            'version' => $data['version'] ?? null,
            'referenceDate' => $data['reference_date'] ?? null,
            'status' => $data['status'] ?? null,
            'errorMessage' => $data['error_message'] ?? null,
            'deleted' => $data['deleted'] ?? null,
        ]);
    }
}
