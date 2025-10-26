<?php

namespace Application\Api\v1\Financial\Exits\Reports\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ExitsReportsRequestsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'reports';



    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];

        foreach ($this->collection as $item)
        {
            $result[] = [
                'id'                    =>  $item->id,
                'accountId'             =>  $item->accountId,
                'reportName'            =>  $item->reportName,
                'detailedReport'        =>  $item->detailedReport,
                'generationDate'        =>  $item->generationDate,
                'dates'                 =>  $item->dates,
                'status'                =>  $item->status,
                'error'                 =>  $item->error,
                'startedBy'             =>  [
                    'id'        =>  $item->userDetail->id,
                    'name'      =>  $item->userDetail->name,
                    'avatar'    =>  $item->userDetail->avatar,
                ],
                'exitTypes'             =>  $item->exitTypes,
                'dateOrder'             =>  $item->dateOrder,
                'linkReport'            =>  $item->linkReport,
                'amounts'               =>  [
                    'totalExits' => $item->amount ? number_format($item->amount, 2, '.', '') : null,
                ],
                'account'               =>  $item->accountId ? [
                    'id'            =>  $item->account->id,
                    'accountType'   =>  $item->account->accountType,
                    'bankName'      =>  $item->account->bankName,
                    'agencyNumber'  =>  $item->account->agencyNumber,
                    'accountNumber' =>  $item->account->accountNumber,
                ] : [
                    'id'            =>  null,
                    'accountType'   =>  null,
                    'bankName'      =>  null,
                    'agencyNumber'  =>  null,
                    'accountNumber' =>  null,
                ],
            ];
        }

        return $result;
    }
}
