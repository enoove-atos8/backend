<?php

namespace Application\Api\v1\Church\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;
class ChurchResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'users';


    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return $this->collection->map(function ($item){


        });
    }


    public function with($request)
    {
        return [
            'total' => count($this)
        ];
    }
}
