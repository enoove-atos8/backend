<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Resources;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class InvoicesResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'invoices';

    private array $monthNames = [
        1 => 'Jan',
        2 => 'Fev',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'Mai',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Set',
        10 => 'Out',
        11 => 'Nov',
        12 => 'Dez',
    ];


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {

        return $this->collection->map(function ($invoice) {
            $date = Carbon::parse($invoice->referenceDate);
            $monthIndex = (int) $date->format('m');

            return [
                'id' => $invoice->id,
                'display' => $this->monthNames[$monthIndex],
                'year' => (int) $date->format('Y'),
                'monthIndex' => (int) $date->format('m') - 1, // 0-based index
                'stringMonth' => $date->format('m'), // 01, 02, ...
                'status' => $invoice->status,
                'amount' => $invoice->amount,
            ];
        })->values()->all();
    }
}
