<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class InvoicesResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'invoices';


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {

        return $this->collection->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'date' => $invoice->date,
                'status' => $invoice->status,
            ];
        });
    }
}
