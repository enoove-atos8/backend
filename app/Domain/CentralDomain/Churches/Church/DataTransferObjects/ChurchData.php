<?php

namespace Domain\CentralDomain\Churches\Church\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ChurchData extends DataTransferObject
{
    /** @var integer | null */
    public ?int $id = 0;

    /** @var string| null  */
    public ?string $tenantId;

    /** @var integer| null  */
    public ?int $planId;

    /** @var string| null  */
    public ?string $name;

    /** @var boolean  */
    public bool $activated;

    /** @var string| null  */
    public ?string $logo;

    /** @var string| null  */
    public ?string $address;

    /** @var string| null  */
    public ?string $cellPhone;

    /** @var string| null  */
    public ?string $mail;

    /** @var string| null  */
    public ?string $docType;

    /** @var string| null  */
    public ?string $docNumber;

    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? 0,
            'tenantId' => $data['tenant_id'] ?? null,
            'planId' => $data['plan_id'] ?? null,
            'name' => $data['name'] ?? null,
            'activated' => $data['activated'] ?? false,
            'logo' => $data['logo'] ?? null,
            'address' => $data['address'] ?? null,
            'cellPhone' => $data['cell_phone'] ?? null,
            'mail' => $data['mail'] ?? null,
            'docType' => $data['doc_type'] ?? null,
            'docNumber' => $data['doc_number'] ?? null,
        ]);
    }
}
