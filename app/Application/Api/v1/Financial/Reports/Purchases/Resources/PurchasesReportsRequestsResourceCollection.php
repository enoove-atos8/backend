<?php

namespace App\Application\Api\v1\Financial\Reports\Purchases\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class PurchasesReportsRequestsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'reports';



    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];

        foreach ($this->collection as $item)
        {
            $result[] = [
                'id'                    =>  $item->id,
                'cardId'                =>  $item->cardId,
                'reportName'            =>  $item->reportName,
                'detailedReport'        =>  $item->detailedReport,
                'generationDate'        =>  $item->generationDate,
                'dates'                 =>  $item->dates,
                'status'                =>  $item->status,
                'error'                 =>  $item->error,
                'startedBy'             =>  [
                    'id'        =>  $item->userDetail->id,
                    'name'      =>  $item->userDetail->name,
                    'avatar'    =>  $item->userDetail->avatar,
                ],
                'dateOrder'             =>  $item->dateOrder,
                'allCardsReceipts'      =>  $item->allCardsReceipts,
                'linkReport'            =>  $item->linkReport,
                'amounts'               =>  [
                    'totalPurchases' => $item->amount ? number_format($item->amount, 2, '.', '') : null,
                ],
                'card'                  =>  $item->card ? [
                    'id'                =>  $item->card->id,
                    'name'              =>  $item->card->name,
                    'cardNumber'        =>  $item->card->cardNumber,
                    'creditCardBrand'   =>  $item->card->creditCardBrand,
                ] : null,
            ];
        }

        return $result;
    }
}
