<?php

namespace Application\Api\v1\Financial\Exits\Exits\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use JsonSerializable;

class AmountByExitTypeResource extends JsonResource
{
    public static $wrap = 'indicators';

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        $result = $this->resource;

        return [
            'payments' => [
                'total' => $result['payments']['total'],
                'accounts' => self::validateAccounts($result['payments']),
            ],
            'transfers' => [
                'total' => $result['transfers']['total'],
                'accounts' => self::validateAccounts($result['transfers']),
            ],
            'ministerialTransfers' => [
                'total' => $result['ministerialTransfers']['total'],
                'accounts' => self::validateAccounts($result['ministerialTransfers']),
            ],
            'contributions' => [
                'total' => $result['contributions']['total'],
                'accounts' => self::validateAccounts($result['contributions']),
            ],
            'anonymous' => [
                'total' => $result['anonymous']['total'],
                'accounts' => self::validateAccounts($result['anonymous']),
            ],
            'accountsTransfer' => [
                'total' => $result['accountsTransfer']['total'],
                'accounts' => self::validateAccounts($result['accountsTransfer']),
            ],
            'total' => $result['total'],
        ];
    }

    /**
     * Validate if accounts array has data
     */
    public function validateAccounts(array $result): ?array
    {
        $accounts = $result['accounts'];

        // Se for Collection, converte para array
        if ($accounts instanceof Collection) {
            $accounts = $accounts->toArray();
        }

        foreach ($accounts as $key => $value) {
            if ($value != null) {
                return $accounts;
            }
        }

        return null;
    }
}
