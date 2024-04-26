<?php

namespace Application\Api\v1\Financial\Entry\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AmountByEntryTypeResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = $this->resource;
        $response = [
            'totalGeneral'  => $result->totalGeneral,
            'amountType'    => $result->amountType,
            'entryType'     => $result->entryType,
        ];

        if($result->entryType == 'tithe'){
            $response['qtdTithes'] = $result->qtdTithes;
            $response['proportionEntriesTithes'] = $result->proportionEntriesTithes;
            $response['qtdTithingMembers'] = $result->qtdTithingMembers;
            $response['proportionEntriesTithesMembers'] = $result->proportionEntriesTithesMembers;
        }
        if($result->entryType == 'offers'){
            $response['qtdOffers'] = $result->qtdOffers;
            $response['proportionEntriesOffers'] = $result->proportionEntriesOffers;
            $response['offersDoNotIdentified'] = $result->offersDoNotIdentified;
        }
        if($result->entryType == 'designated'){
            $response['qtdDesignated'] = $result->qtdDesignated;
            $response['proportionEntriesDesignated'] = $result->proportionEntriesDesignated;
            $response['designatedOfDevolutions'] = $result->designatedOfDevolutions;
        }

        return $response;
    }
}
