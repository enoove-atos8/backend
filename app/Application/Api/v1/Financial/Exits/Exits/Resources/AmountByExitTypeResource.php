<?php

namespace Application\Api\v1\Financial\Exits\Exits\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AmountByExitTypeResource extends JsonResource
{
    public static $wrap = 'indicators';



    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        $result = $this->resource;

        return [
            'payments'              => [
                'total' => $result['payments']['total'],
                'accounts' => self::validateAccounts($result['payments']),
            ],
            'transfers'             => [
                'total' => $result['transfers']['total'],
                'accounts' => self::validateAccounts($result['transfers']),
            ],
            'ministerialTransfers'  => [
                'total' => $result['ministerialTransfers']['total'],
                'accounts' => self::validateAccounts($result['ministerialTransfers']),
            ],
            'contributions'         => [
                'total' => $result['contributions']['total'],
                'accounts' => self::validateAccounts($result['contributions']),
            ],
            'total'                 => $result['total'],
        ];
    }


    /**
     * Validate if accounts array has data
     *
     * @param array $result
     * @return array|null
     */
    public function validateAccounts(array $result): ?array
    {
        $accounts = $result['accounts'];

        // Se for Collection, converte para array
        if ($accounts instanceof \Illuminate\Support\Collection) {
            $accounts = $accounts->toArray();
        }

        foreach ($accounts as $key => $value) {
            if($value != null)
            {
                return $accounts;
            }
        }

        return null;
    }
}
