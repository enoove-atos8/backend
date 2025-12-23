<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AmountRequestIndicatorsResource extends JsonResource
{
    /**
     * Replace the 'data' key in the JSON response
     */
    public static $wrap = 'indicators';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = $this->resource;

        return [
            'total' => $result->total,
            'totalAmount' => $result->totalAmount,
            'pending' => $result->pending,
            'approved' => $result->approved,
            'rejected' => $result->rejected,
            'transferred' => $result->transferred,
            'partiallyProven' => $result->partiallyProven,
            'proven' => $result->proven,
            'overdue' => $result->overdue,
            'closed' => $result->closed,
        ];
    }
}
