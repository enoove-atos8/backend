<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class AmountRequestResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     */
    public static $wrap = 'amountRequests';

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = ['data' => []];

        foreach ($this->collection as $item) {
            $result['data'][] = $this->formatItem($item);
        }

        return $result;
    }

    /**
     * Format an item for the API response
     */
    private function formatItem($item): array
    {
        $requestedAmount = (float) ($item->requestedAmount ?? 0);
        $provenAmount = (float) ($item->provenAmount ?? 0);
        $devolutionAmount = (float) ($item->devolutionAmount ?? 0);
        $remainingToProve = max(0, $requestedAmount - $provenAmount - $devolutionAmount);

        return [
            'id' => $item->id ?? null,
            'memberId' => $item->memberId ?? null,
            'groupId' => $item->groupId ?? null,
            'type' => $item->type ?? null,
            'typeLabel' => isset($item->type) ? (ReturnMessages::TYPE_LABELS[$item->type] ?? $item->type) : null,
            'requestedAmount' => $item->requestedAmount ?? null,
            'description' => $item->description ?? null,
            'proofDeadline' => $item->proofDeadline ?? null,
            'status' => $item->status ?? null,
            'statusLabel' => isset($item->status) ? (ReturnMessages::STATUS_LABELS[$item->status] ?? $item->status) : null,
            'provenAmount' => $item->provenAmount ?? null,
            'devolutionAmount' => $item->devolutionAmount ?? null,
            'remainingToProve' => number_format($remainingToProve, 2, '.', ''),
            'createdAt' => $item->createdAt ?? null,
            'approvedAt' => $item->approvedAt ?? null,
            'transferredAt' => $item->transferredAt ?? null,
            'member' => $this->getMemberData($item),
            'group' => $this->getGroupData($item),
            'transferExit' => $this->getTransferExitData($item),
        ];
    }

    /**
     * Get member data if available
     */
    private function getMemberData($item): ?array
    {
        if (isset($item->member) && $item->member->id) {
            return [
                'id' => $item->member->id,
                'fullName' => $item->member->fullName,
                'avatar' => $item->member->avatar,
            ];
        }

        return null;
    }

    /**
     * Get group data if available
     */
    private function getGroupData($item): ?array
    {
        if (isset($item->group) && $item->group->id) {
            return [
                'id' => $item->group->id,
                'name' => $item->group->name,
            ];
        }

        return null;
    }

    /**
     * Get transfer exit data if available
     */
    private function getTransferExitData($item): ?array
    {
        if (isset($item->transferExit) && $item->transferExit !== null) {
            return [
                'id' => $item->transferExit['id'],
                'exitType' => $item->transferExit['exitType'],
                'amount' => $item->transferExit['amount'],
                'transactionType' => $item->transferExit['transactionType'],
                'dateTransactionCompensation' => $item->transferExit['dateTransactionCompensation'],
                'comments' => $item->transferExit['comments'],
                'receiptLink' => $item->transferExit['receiptLink'],
            ];
        }

        return null;
    }
}
