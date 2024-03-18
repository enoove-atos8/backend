<?php

namespace App\Application\Api\v1\Financial\Reviewer\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class FinancialReviewerResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'reviewers';



    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($item){
            return [
                'id'            =>  $item->id,
                'fullName'      =>  $item->full_name,
                'reviewerType'  =>  $item->reviewer_type,
                'avatar'        =>  $item->avatar,
                'gender'        =>  $item->gender,
                'cpf'           =>  $item->cpf,
                'rg'            =>  $item->rg,
                'email'         =>  $item->email,
                'cellPhone'     =>  $item->cell_phone,
                'activated'     =>  $item->activated,
                'deleted'       =>  $item->deleted,
            ];
        });
    }
}
