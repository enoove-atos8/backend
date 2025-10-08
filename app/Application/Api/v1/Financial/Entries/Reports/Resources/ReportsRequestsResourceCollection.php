<?php

namespace Application\Api\v1\Financial\Entries\Reports\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ReportsRequestsResourceCollection extends ResourceCollection
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
                'reportName'            =>  $item->reportName,
                'detailedReport'        =>  $item->detailedReport,
                'generationDate'        =>  $item->generationDate,
                'dates'                 =>  $item->dates,
                'status'                =>  $item->status,
                'startedBy'             =>  [
                    'id'        =>  $item->userDetail->id,
                    'name'      =>  $item->userDetail->name,
                    'avatar'    =>  $item->userDetail->avatar,
                ],
                'entryTypes'            =>  $item->entryTypes,
                'groupReceivedId'       =>  $item->groupReceivedId,
                'group'                 =>  $this->getGroups($item),
                'dateOrder'             =>  $item->dateOrder,
                'includedCashDeposit'   =>  $item->includeCashDeposit,
                'linkReport'            =>  $item->linkReport,
                'entryTypesAmounts'     =>  [
                    'titheAmount'       =>  $item->titheAmount,
                    'designatedAmount'  =>  $item->designatedAmount,
                    'offerAmount'       =>  $item->offerAmount,
                ],
                'monthlyEntriesAmount'    =>  $item->monthlyEntriesAmount,
                'account'     =>  [
                    'id'            =>  $item->account->id,
                    'accountType'   =>  $item->account->accountType,
                    'bankName'      =>  $item->account->bankName,
                    'agencyNumber'  =>  $item->account->agencyNumber,
                    'accountNumber' =>  $item->account->accountNumber,
                ],
            ];
        }

        return $result;
    }



    /**
     * @param mixed $requestReport $
     * @return array|null
     */
    public function getGroups(mixed $requestReport): ?array
    {
        if(!is_null($requestReport->group))
        {
            return [
                'id'     =>  $requestReport->group->id,
                'name'   =>  $requestReport->group->name,
            ];
        }
        else
        {
            return null;
        }
    }
}
