<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AmountRequestResource extends JsonResource
{
    /**
     * Replace the 'data' key in the JSON response
     */
    public static $wrap = 'amountRequest';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = $this->resource;

        $requestedAmount = (float) ($result->requestedAmount ?? 0);
        $provenAmount = (float) ($result->provenAmount ?? 0);
        $devolutionAmount = (float) ($result->devolutionAmount ?? 0);
        $remainingToProve = max(0, $requestedAmount - $provenAmount - $devolutionAmount);

        return [
            'id' => $result->id,
            'memberId' => $result->memberId,
            'groupId' => $result->groupId,
            'requestedAmount' => $result->requestedAmount,
            'description' => $result->description,
            'proofDeadline' => $result->proofDeadline,
            'status' => $result->status,
            'statusLabel' => ReturnMessages::STATUS_LABELS[$result->status] ?? $result->status,
            'approvedBy' => $result->approvedBy,
            'approvedAt' => $result->approvedAt,
            'rejectionReason' => $result->rejectionReason,
            'transferExitId' => $result->transferExitId,
            'transferredAt' => $result->transferredAt,
            'provenAmount' => $result->provenAmount,
            'devolutionEntryId' => $result->devolutionEntryId,
            'devolutionAmount' => $result->devolutionAmount,
            'remainingToProve' => number_format($remainingToProve, 2, '.', ''),
            'closedBy' => $result->closedBy,
            'closedAt' => $result->closedAt,
            'requestedBy' => $result->requestedBy,
            'createdAt' => $result->createdAt,
            'updatedAt' => $result->updatedAt,
            'member' => $this->getMemberData($result),
            'group' => $this->getGroupData($result),
            'transferExit' => $this->getTransferExitData($result),
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
                'cellPhone' => $item->member->cellPhone,
                'email' => $item->member->email,
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
                'slug' => $item->group->slug,
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
