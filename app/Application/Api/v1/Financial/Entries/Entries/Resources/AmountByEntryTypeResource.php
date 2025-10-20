<?php

namespace Application\Api\v1\Financial\Entries\Entries\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AmountByEntryTypeResource extends JsonResource
{
    public static $wrap = 'entriesIndicators';
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = $this->resource;

        return [
            'tithes'        => [
                'total' => $result['tithes']['total'],
                'accounts' => self::validateAccounts($result['tithes']),
            ],
            'offers'        => [
                'total' => $result['offers']['total'],
                'accounts' => self::validateAccounts($result['offers']),
            ],
            'designated'        => [
                'total' => $result['designated']['total'],
                'accounts' => self::validateAccounts($result['designated']),
            ],
            'anonymousOffers'   => [
                'total' => $result['anonymousOffers']['total'],
                'accounts' => self::validateAccounts($result['anonymousOffers']),
            ],
            'accountsTransfers' => [
                'total' => $result['accountsTransfers']['total'],
                'accounts' => self::validateAccounts($result['accountsTransfers']),
            ],
        ];
    }


    public function validateAccounts(array $result)
    {
        foreach ($result['accounts'] as $key => $value) {
            if($value != null)
            {
                return $result['accounts'];
            }
        }

        return null;
    }
}
