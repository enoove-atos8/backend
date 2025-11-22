<?php

namespace Domain\CentralDomain\Churches\Church\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ChurchData extends DataTransferObject
{
    public ?int $id = 0;

    public ?string $tenantId;

    public ?int $planId;

    public ?string $name;

    public bool $activated;

    public ?string $logo;

    public ?string $address;

    public ?string $cellPhone;

    public ?string $mail;

    public ?string $docType;

    public ?string $docNumber;

    public ?string $stripeId;

    public ?string $paymentMethodId;

    public ?int $memberCount;

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
            'stripeId' => $data['stripe_id'] ?? null,
            'paymentMethodId' => $data['payment_method_id'] ?? null,
            'memberCount' => $data['member_count'] ?? null,
        ]);
    }
}
