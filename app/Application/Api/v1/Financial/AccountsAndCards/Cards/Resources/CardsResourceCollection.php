<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Cards\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class CardsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'cards';


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {

        return $this->collection->map(function ($card) {
            return [
                'id' => $card->id,
                'name' => $card->name,
                'description' => $card->description,
                'cardNumber' => $card->cardNumber,
                'expiryDate' => $card->expiryDate,
                'closingDate' => $card->closingDate,
                'status' => $card->status,
                'active' => (bool) $card->active,
                'creditCardBrand' => $card->creditCardBrand,
                'personType' => $card->personType,
                'cardHolderName' => $card->cardHolderName,
                'limit' => (float) $card->limit,
            ];
        });
    }
}
