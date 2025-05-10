<?php

namespace Application\Api\v1\Financial\Movements\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class MovementResourceCollection extends ResourceCollection
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
        return [
            'data' => $this->collection->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'groupId' => $movement->groupId,
                    'entryId' => $movement->entryId,
                    'exitId' => $movement->exitId,
                    'type' => $movement->type,
                    'subType' => $movement->subType,
                    'amount' => $movement->amount,
                    'balance' => $movement->balance,
                    'description' => $movement->description,
                    'movementDate' => $movement->movementDate,
                    'isInitialBalance' => (bool) $movement->isInitialBalance,
                    'deleted' => (bool) $movement->deleted,
                ];
            })
        ];
    }
}
