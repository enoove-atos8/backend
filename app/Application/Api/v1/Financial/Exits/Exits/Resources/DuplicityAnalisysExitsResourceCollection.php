<?php

namespace Application\Api\v1\Financial\Exits\Exits\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JsonSerializable;

class DuplicityAnalisysExitsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'duplicities';


    public function __construct($resource)
    {
        parent::__construct($resource);
    }


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];
        $duplicities = $this->collection;

        foreach ($duplicities as $item)
        {
            $result[] = [
                'exitType'                          =>  $item->exitType,
                'amount'                            =>  $item->amount,
                'transactionType'                   =>  $item->transactionType,
                'dateTransactionCompensation'       =>  $item->dateTransactionCompensation,
                'divisionId'                        =>  $item->divisionId,
                'divisionName'                      =>  $item->divisionName,
                'groupId'                           =>  $item->groupId,
                'groupName'                         =>  $item->groupName,
                'paymentCategoryId'                 =>  $item->paymentCategoryId,
                'paymentItemId'                     =>  $item->paymentItemId,
                'repetitionCount'                   =>  $item->repetitionCount,
                'duplicityVerified'                 =>  $item->duplicityVerified,
                'duplicateIds'                      =>  $item->duplicateIds,
            ];
        }

        return $result;
    }
}
