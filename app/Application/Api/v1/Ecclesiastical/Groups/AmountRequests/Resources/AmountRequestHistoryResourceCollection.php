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
     * Mapeamento de evento para status
     */
    private const EVENT_STATUS_MAP = [
        ReturnMessages::HISTORY_EVENT_CREATED => ReturnMessages::STATUS_PENDING,
        ReturnMessages::HISTORY_EVENT_APPROVED => ReturnMessages::STATUS_APPROVED,
        ReturnMessages::HISTORY_EVENT_REJECTED => ReturnMessages::STATUS_REJECTED,
        ReturnMessages::HISTORY_EVENT_TRANSFERRED => ReturnMessages::STATUS_TRANSFERRED,
        ReturnMessages::HISTORY_EVENT_CLOSED => ReturnMessages::STATUS_CLOSED,
        ReturnMessages::HISTORY_EVENT_DEVOLUTION_LINKED => ReturnMessages::STATUS_CLOSED,
    ];

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = ['data' => []];

        foreach ($this->collection as $item) {
            $status = $this->getStatusFromEvent($item->event, $item->metadata);

            $result['data'][] = [
                'id' => $item->id ?? null,
                'amountRequestId' => $item->amountRequestId ?? null,
                'event' => $item->event ?? null,
                'description' => $item->description ?? null,
                'userId' => $item->userId ?? null,
                'userName' => $item->userName ?? null,
                'metadata' => $item->metadata ?? null,
                'status' => $status,
                'statusLabel' => $status ? (ReturnMessages::STATUS_LABELS[$status] ?? $status) : null,
                'createdAt' => $item->createdAt ?? null,
            ];
        }

        return $result;
    }

    /**
     * Get status from event type
     */
    private function getStatusFromEvent(?string $event, ?array $metadata): ?string
    {
        if ($event === null) {
            return null;
        }

        // Para eventos de comprovante, verificar se há status no metadata
        if (in_array($event, [
            ReturnMessages::HISTORY_EVENT_RECEIPT_ADDED,
            ReturnMessages::HISTORY_EVENT_RECEIPT_UPDATED,
            ReturnMessages::HISTORY_EVENT_RECEIPT_DELETED,
        ])) {
            return $metadata['new_status'] ?? ReturnMessages::STATUS_PARTIALLY_PROVEN;
        }

        // Para exit_unlinked, volta para approved
        if ($event === ReturnMessages::HISTORY_EVENT_EXIT_UNLINKED) {
            return ReturnMessages::STATUS_APPROVED;
        }

        return self::EVENT_STATUS_MAP[$event] ?? null;
    }
}
