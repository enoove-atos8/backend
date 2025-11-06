<?php

namespace App\Application\Api\v1\Financial\Reports\Balances\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class BalancesReportsRequestsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     *
     * @var string
     */
    public static $wrap = 'reports';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];

        foreach ($this->collection as $item) {
            $result[] = [
                'id' => $item->id,
                'accountId' => $item->accountId,
                'reportName' => $item->reportName,
                'generationDate' => $item->generationDate,
                'dates' => $item->dates,
                'status' => $item->status,
                'error' => $item->error,
                'startedBy' => [
                    'id' => $item->userDetail->id,
                    'name' => $item->userDetail->name,
                    'avatar' => $item->userDetail->avatar,
                ],
                'linkReport' => $item->linkReport,
                'account' => $item->accountId ? [
                    'id' => $item->account->id,
                    'accountType' => $item->account->accountType,
                    'bankName' => $item->account->bankName,
                    'agencyNumber' => $item->account->agencyNumber,
                    'accountNumber' => $item->account->accountNumber,
                ] : [
                    'id' => null,
                    'accountType' => null,
                    'bankName' => null,
                    'agencyNumber' => null,
                    'accountNumber' => null,
                ],
            ];
        }

        return $result;
    }
}
