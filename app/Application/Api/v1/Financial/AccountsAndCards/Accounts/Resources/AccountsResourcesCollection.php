<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Accounts\Resources;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class AccountsResourcesCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'accounts';


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->collection->map(function (AccountData $account) {
            return [
                'id' => $account->id,
                'accountType' => $account->accountType,
                'bankName' => $account->bankName,
                'agencyNumber' => $account->agencyNumber,
                'accountNumber' => $account->accountNumber,
                'activated' => $account->activated,
            ];
        });
    }
}
