<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class AmountRequestHistoryResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     */
    public static $wrap = 'history';

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = ['data' => []];

        foreach ($this->collection as $item) {
            $result['data'][] = [
                'id' => $item->id ?? null,
                'amountRequestId' => $item->amountRequestId ?? null,
                'event' => $item->event ?? null,
                'description' => $item->description ?? null,
                'userId' => $item->userId ?? null,
                'userName' => $item->userName ?? null,
                'metadata' => $item->metadata ?? null,
                'status' => $item->status ?? null,
                'statusLabel' => isset($item->status) ? (ReturnMessages::STATUS_LABELS[$item->status] ?? $item->status) : null,
                'createdAt' => $item->createdAt ?? null,
            ];
        }

        return $result;
    }
}
