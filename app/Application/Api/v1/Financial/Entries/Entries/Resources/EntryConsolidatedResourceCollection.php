<?php

namespace Application\Api\v1\Financial\Entries\Entries\Resources;

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
        $data[] =  $this->collection->map(function ($item){

            return [
                'id'                        =>  $item->id,
                'date'                      =>  $item->date,
                'monthName'                 =>  $this->getMonthName($item->date),
                'consolidated'              =>  $item->consolidated,
                'entriesNoCompensate'       =>  $item->entriesNoCompensate,
                'amountEntriesNoCompensate' =>  $item->amountEntriesNoCompensate,
                'amountEntries'             =>  $item->amountEntries,
            ];
        });

        return [
            'qtdMonths'    =>  $this->resource->count(),
            'totalAmount'   =>  $this->resource->sum('amountEntries'),
            'data'          =>  $data[0]
        ];
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

        return $monthNames[$monthIndex];
    }


    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
