<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\DataTransferObjects;

use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GroupData extends DataTransferObject
{
    const ID_PROPERTY = 'id';

    const NAME_PROPERTY = 'name';

    const GROUPS_NAME_PROPERTY = 'groups_name';

    const DIVISION_ID_PROPERTY = 'groups_division_id';

    public ?int $id;

    public ?int $divisionId;

    public ?int $parentGroupId;

    public ?int $leaderId;

    public ?string $name;

    public ?string $description;

    public ?string $slug;

    public ?bool $financialMovement;

    public ?bool $enabled;

    public ?bool $temporaryEvent;

    public ?bool $returnValues;

    public ?bool $financialGroup;

    public ?string $startDate;

    public ?string $endDate;

    public ?MemberData $leader;

    /**
     * Create a GroupData instance from response data
     *
     * @param  array  $data  Response data from repository
     * @return self New GroupData instance
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        $groupData = [
            'id' => $data['groups_id'] ?? null,
            'divisionId' => $data['groups_division_id'] ?? null,
            'parentGroupId' => $data['groups_parent_group_id'] ?? null,
            'leaderId' => $data['groups_leader_id'] ?? null,
            'name' => $data['groups_name'] ?? null,
            'description' => $data['groups_description'] ?? null,
            'slug' => $data['groups_slug'] ?? null,
            'financialMovement' => isset($data['groups_financial_transactions_exists']) ? (bool) $data['groups_financial_transactions_exists'] : null,
            'enabled' => isset($data['groups_enabled']) ? (bool) $data['groups_enabled'] : null,
            'temporaryEvent' => isset($data['groups_temporary_event']) ? (bool) $data['groups_temporary_event'] : null,
            'returnValues' => isset($data['groups_return_values']) ? (bool) $data['groups_return_values'] : null,
            'financialGroup' => isset($data['groups_financial_group']) ? (bool) $data['groups_financial_group'] : null,
            'startDate' => $data['groups_start_date'] ?? null,
            'endDate' => $data['groups_end_date'] ?? null,
        ];

        if (isset($data['members_id'])) {
            $groupData['leader'] = MemberData::fromResponse($data);
        }

        return new self($groupData);
    }
}
