<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\DataTransferObjects;

use Carbon\Carbon;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ExtractorFileData extends DataTransferObject
{
    /** @var string */
    public string $movementDate;

    /** @var string */
    public string $description;

    /** @var float */
    public float $amount;

    /** @var string */
    public string $type;

    /** @var string|null */
    public ?string $documentNumber;

    /** @var string|null */
    public ?string $account;

    /**
     * Create an ExtractorFileData instance from file row
     *
     * @param array $row
     * @return self
     * @throws UnknownProperties
     */
    public static function fromFile(array $row): self
    {
        return new self([
            'movementDate' => Carbon::createFromFormat('Ymd', trim($row[1], '"'))->format('Y-m-d'),
            'description' => trim($row[3], '"'),
            'amount' => (float) str_replace(',', '.', trim($row[4], '"')),
            'type' => trim($row[5], '"'),
            'documentNumber' => trim($row[2], '"'),
            'account' => trim($row[0], '"'),
        ]);
    }
}
