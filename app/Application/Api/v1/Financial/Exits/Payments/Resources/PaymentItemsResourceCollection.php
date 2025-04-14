<?php

namespace Application\Api\v1\Financial\Exits\Payments\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class PaymentItemsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'items';


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];
        $items = $this->collection;

        foreach ($items as $item)
        {
            $result[] = [
                'id'                    =>  $item->id,
                'payment_category_id'   =>  $item->paymentCategoryId,
                'slug'                  =>  $item->slug,
                'name'                  =>  $item->name,
                'description'           =>  $item->description,
            ];
        }

        return $result;
    }
}
