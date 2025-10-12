<?php

namespace App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Resources\Files;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class AccountsFilesResourcesCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'files';


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->collection->map(function (AccountFileData $accountFileData) {
            return [
                'id'                    => $accountFileData->id,
                'accountId'             => $accountFileData->accountId,
                'originalFilename'      => $accountFileData->originalFilename,
                'link'                  => $accountFileData->link,
                'fileType'              => $accountFileData->fileType,
                'version'               => $accountFileData->version,
                'referenceDate'         => $accountFileData->referenceDate,
                'status'                => $accountFileData->status,
                'errorMessage'          => $accountFileData->errorMessage,
                'deleted'               => $accountFileData->deleted,
                'account'               => $this->extractAccountData($accountFileData),
            ];
        });
    }



    /**
     * Extract account data from AccountFileData
     *
     * @param AccountFileData $accountFileData
     * @return array|null
     */
    private function extractAccountData(AccountFileData $accountFileData): ?array
    {
        if (!$accountFileData->account) {
            return null;
        }

        return [
            'id'            => $accountFileData->account->id,
            'accountType'   => $accountFileData->account->accountType,
            'bankName'      => $accountFileData->account->bankName,
            'agencyNumber'  => $accountFileData->account->agencyNumber,
            'accountNumber' => $accountFileData->account->accountNumber,
        ];
    }
}
