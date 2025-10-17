<?php

namespace App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Resources\Movements;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\MovementsData;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class MovementsResourcesCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'movements';


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->collection->map(function (MovementsData $movement) {
            return [
                'id' => $movement->id,
                'accountId' => $movement->accountId,
                'fileId' => $movement->fileId,
                'movementDate' => $movement->movementDate,
                'transactionType' => $movement->transactionType,
                'description' => $movement->description,
                'amount' => $movement->amount,
                'movementType' => $movement->movementType,
                'anonymous' => $movement->anonymous,
                'conciliatedStatus' => $movement->conciliatedStatus,
                'createdAt' => $movement->createdAt,
                'updatedAt' => $movement->updatedAt,
            ];
        });
    }
}
