<?php

namespace Domain\Ecclesiastical\Divisions\DataTransferObjects;

use Illuminate\Support\Facades\Log;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DivisionData extends DataTransferObject
{
    /** @var null | integer  */
    public ?int $id;

    /** @var null | string  */
    public ?string $slug;

    /** @var null | string  */
    public ?string $name;

    /** @var null | string  */
    public ?string $description;

    /** @var null | boolean  */
    public ?bool $requireLeader;

    /** @var null | boolean  */
    public ?bool $enabled;

    /**
     * Create a DivisionData instance from response data
     *
     * @param array $data Response data from repository
     * @return self New DivisionData instance
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'id' => $data['division_id'] ?? null,
            'slug' => $data['division_slug'] ?? null,
            'name' => $data['division_name'] ?? null,
            'description' => $data['division_description'] ?? null,
            'enabled' => isset($data['division_enabled']) ? (bool)$data['division_enabled'] : null,
            'requireLeader' => isset($data['require_leader']) ? (bool)$data['require_leader'] : null,
        ]);
    }
}
