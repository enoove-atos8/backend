<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class AmountRequestReminderResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     */
    public static $wrap = 'reminders';

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
                'type' => $item->type ?? null,
                'typeLabel' => isset($item->type) ? (ReturnMessages::REMINDER_TYPE_LABELS[$item->type] ?? $item->type) : null,
                'channel' => $item->channel ?? null,
                'channelLabel' => isset($item->channel) ? (ReturnMessages::CHANNEL_LABELS[$item->channel] ?? $item->channel) : null,
                'scheduledAt' => $item->scheduledAt ?? null,
                'sentAt' => $item->sentAt ?? null,
                'status' => $item->status ?? null,
                'statusLabel' => isset($item->status) ? (ReturnMessages::REMINDER_STATUS_LABELS[$item->status] ?? $item->status) : null,
                'errorMessage' => $item->errorMessage ?? null,
                'metadata' => $item->metadata ?? null,
                'createdAt' => $item->createdAt ?? null,
            ];
        }

        return $result;
    }
}
