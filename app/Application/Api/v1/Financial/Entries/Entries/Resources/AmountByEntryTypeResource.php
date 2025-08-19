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
                'accounts' => self::validateTithes($result['tithes']),
            ],
            'offers'        => [
                'total' => $result['offers']['total'],
                'accounts' => self::validateOffers($result['offers']),
            ],
            'designated'        => [
                'total' => $result['designated']['total'],
                'accounts' => self::validateDesignated($result['designated']),
            ],
        ];
    }


    public function validateTithes(array $result)
    {
        foreach ($result['accounts'] as $key => $value) {
            if($value != null)
            {
                return $result['accounts'];
            }
        }

        return null;
    }



    public function validateOffers(array $result)
    {
        foreach ($result['accounts'] as $key => $value) {
            if($value != null)
            {
                return $result['accounts'];
            }
        }

        return null;
    }



    public function validateDesignated(array $result)
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
