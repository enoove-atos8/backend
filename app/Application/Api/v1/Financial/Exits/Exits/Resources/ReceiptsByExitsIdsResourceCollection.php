<?php

namespace Application\Api\v1\Financial\Exits\Exits\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ReceiptsByExitsIdsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'receipts';


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
        $receipts = $this->collection;

        foreach ($receipts as $item)
        {
            $result[] = [
                'id'        =>  $item->id,
                'receipt'   =>  $item->receipt,
            ];
        }

        return $result;
    }
}
