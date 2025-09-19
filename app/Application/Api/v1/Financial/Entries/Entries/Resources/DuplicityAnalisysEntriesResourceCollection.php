<?php

namespace Application\Api\v1\Financial\Entries\Entries\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JsonSerializable;

class DuplicityAnalisysEntriesResourceCollection extends ResourceCollection
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
                'entryType'                         =>  $item->entryType,
                'amount'                            =>  $item->amount,
                'transactionType'                   =>  $item->transactionType,
                'dateTransactionCompensation'       =>  $item->dateTransactionCompensation,
                'memberId'                          =>  $item->memberId,
                'memberFullName'                    =>  $item->memberFullName,
                'repetitionCount'                   =>  $item->repetitionCount,
                'groupReturnedId'                   =>  $item->groupReturnedId,
                'groupReceivedId'                   =>  $item->groupReceivedId,
                'groupReceivedName'                 =>  $item->groupReceivedName,
                'duplicityVerified'                 =>  $item->duplicityVerified,
                'duplicateIds'                      =>  $item->duplicateIds,
            ];
        }

        return $result;
    }
}
