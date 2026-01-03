<?php

namespace Domain\Financial\Exits\DuplicitiesAnalisys\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ReceiptsByIdsData extends DataTransferObject
{
    /** @var integer | null */
    public int | null $id;

    /** @var string | null */
    public string | null $receipt;



    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'receipt' => $data['receipt_link'] ?? null,
        ]);
    }
}
