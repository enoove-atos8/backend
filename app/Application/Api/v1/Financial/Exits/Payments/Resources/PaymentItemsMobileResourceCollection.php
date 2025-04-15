<?php

namespace Application\Api\v1\Financial\Exits\Payments\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class PaymentItemsMobileResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'items';


    public function __construct($resource)
    {
        parent::__construct($resource);
    }


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        $result = [];
        $payments = $this->collection;

        foreach ($payments as $payment)
        {
            $result[] = [
                'slugPage'              =>  $payment->slug,
                'titleCard'             =>  $payment->name,
                'descCard'              =>  $payment->description,
                'paymentType'           =>  null,
                'paymentCategoryId'     =>  $payment->id,
                'paymentItemId'         =>  null,
            ];
        }

        return $result;
    }
}
