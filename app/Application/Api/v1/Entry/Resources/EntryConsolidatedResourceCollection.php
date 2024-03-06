<?php

namespace Application\Api\v1\Entry\Resources;

use Domain\Members\Models\Member;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class EntryConsolidatedResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'entries';


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {

        return $this->collection->map(function ($item, $result) {
            return [
                'id'                   =>  $item->id,
                'date'                 =>  $item->date,
                'monthName'            =>  $this->getMonthName($item->date),
                'consolidated'         =>  $item->consolidated,
                'designatedAmount'     =>  $item->designated_amount,
                'offersAmount'         =>  $item->offers_amount,
                'titheAmount'          =>  $item->tithe_amount,
                'totalAmount'          =>  $item->total_amount,
                'entriesNoCompensate'  =>  $item->entriesNoCompensate,
            ];
        });
    }


    /**
     * @param mixed $date
     * @return string
     */
    public function getMonthName(mixed $date): string
    {
        $monthIndex = intval(substr($date, 5, 6)) - 1;
        $monthNames = [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
        ];

        $monthName = $monthNames[$monthIndex];
        return $monthName;
    }


    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
