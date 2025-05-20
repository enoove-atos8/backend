<?php

namespace App\Application\Api\v1\Financial\AccountsAndCards\Cards\Resources;

use Domain\Members\Models\Member;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CardResource extends JsonResource
{
    public static $wrap = 'card';



    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $card = $this->resource;

        return [
            'id' => $card->id,
            'name' => $card->name,
            'description' => $card->description,
            'cardNumber' => $card->cardNumber,
            'expiryDate' => $card->expiryDate,
            'dueDay' => $card->dueDay,
            'closingDay' => $card->closingDay,
            'status' => $card->status,
            'active' => (bool) $card->active,
            'deleted' => (bool) $card->deleted,
            'creditCardBrand' => $card->creditCardBrand,
            'personType' => $card->personType,
            'cardHolderName' => $card->cardHolderName,
            'limit' => (float) $card->limit,
        ];
    }
}
