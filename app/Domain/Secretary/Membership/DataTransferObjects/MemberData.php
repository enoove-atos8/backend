<?php

namespace Domain\Secretary\Membership\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MemberData extends DataTransferObject
{
    public const ID = 'id';

    public const ACTIVATED = 'activated';

    public const DELETED = 'deleted';

    public const AVATAR = 'avatar';

    public const FULL_NAME = 'fullName';

    public const GENDER = 'gender';

    public const CPF = 'cpf';

    public const MIDDLE_CPF = 'middleCpf';

    public const RG = 'rg';

    public const WORK = 'work';

    public const BORN_DATE = 'bornDate';

    public const EMAIL = 'email';

    public const PHONE = 'phone';

    public const CELL_PHONE = 'cellPhone';

    public const ADDRESS = 'address';

    public const DISTRICT = 'district';

    public const CITY = 'city';

    public const UF = 'uf';

    public const MARITAL_STATUS = 'maritalStatus';

    public const SPOUSE = 'spouse';

    public const FATHER = 'father';

    public const MOTHER = 'mother';

    public const ECCLESIASTICAL_FUNCTION = 'ecclesiasticalFunction';

    public const MEMBER_TYPE = 'memberType';

    public const BAPTISM_DATE = 'baptismDate';

    public const BLOOD_TYPE = 'bloodType';

    public const EDUCATION = 'education';

    public const GROUP_IDS = 'groupIds';

    public int $id = 0;

    public ?bool $activated;

    public ?bool $deleted;

    public ?string $avatar;

    public ?string $fullName;

    public ?string $gender;

    public ?string $cpf;

    public ?string $middleCpf;

    public ?string $rg;

    public ?string $work;

    public ?string $bornDate;

    public ?string $email;

    public ?string $phone;

    public ?string $cellPhone;

    public ?string $address;

    public ?string $district;

    public ?string $city;

    public ?string $uf;

    public ?string $maritalStatus;

    public ?string $spouse;

    public ?string $father;

    public ?string $mother;

    public array|string|null $ecclesiasticalFunction;

    public ?string $memberType;

    public ?string $baptismDate;

    public ?string $bloodType;

    public ?string $education;

    public array|string|null $groupIds;

    public ?float $titheAmount;

    /**
     * Create a MemberData instance from response data
     *
     * @param  array  $data  Response data from repository
     * @return array New MemberData instance
     */
    private static function getMemberPrefixedData(array $data): array
    {
        return [
            'activated' => isset($data['members_activated']) ? (bool) $data['members_activated'] : null,
            'deleted' => isset($data['members_deleted']) ? (bool) $data['members_deleted'] : null,
            'avatar' => $data['members_avatar'] ?? null,
            'fullName' => $data['members_full_name'] ?? null,
            'gender' => $data['members_gender'] ?? null,
            'cpf' => $data['members_cpf'] ?? null,
            'middleCpf' => $data['members_middle_cpf'] ?? null,
            'rg' => $data['members_rg'] ?? null,
            'work' => $data['members_work'] ?? null,
            'bornDate' => $data['members_born_date'] ?? null,
            'email' => $data['members_email'] ?? null,
            'phone' => $data['members_phone'] ?? null,
            'cellPhone' => $data['members_cell_phone'] ?? null,
            'address' => $data['members_address'] ?? null,
            'district' => $data['members_district'] ?? null,
            'city' => $data['members_city'] ?? null,
            'uf' => $data['members_uf'] ?? null,
            'maritalStatus' => $data['members_marital_status'] ?? null,
            'spouse' => $data['members_spouse'] ?? null,
            'father' => $data['members_father'] ?? null,
            'mother' => $data['members_mother'] ?? null,
            'ecclesiasticalFunction' => $data['members_ecclesiastical_function'] ?? null,
            'memberType' => $data['members_member_type'] ?? null,
            'baptismDate' => $data['members_baptism_date'] ?? null,
            'bloodType' => $data['members_blood_type'] ?? null,
            'education' => $data['members_education'] ?? null,
            'groupIds' => isset($data['members_group_ids']) ? (is_string($data['members_group_ids']) ? json_decode($data['members_group_ids'], true) : $data['members_group_ids']) : null,
            'titheAmount' => $data['tithe_amount'] ?? null,
        ];
    }

    private static function getNonPrefixedData(array $data): array
    {
        return [
            'activated' => isset($data['activated']) ? (bool) $data['activated'] : null,
            'deleted' => isset($data['deleted']) ? (bool) $data['deleted'] : null,
            'avatar' => $data['avatar'] ?? null,
            'fullName' => $data['full_name'] ?? null,
            'gender' => $data['gender'] ?? null,
            'cpf' => $data['cpf'] ?? null,
            'middleCpf' => $data['middle_cpf'] ?? null,
            'rg' => $data['rg'] ?? null,
            'work' => $data['work'] ?? null,
            'bornDate' => $data['born_date'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'cellPhone' => $data['cell_phone'] ?? null,
            'address' => $data['address'] ?? null,
            'district' => $data['district'] ?? null,
            'city' => $data['city'] ?? null,
            'uf' => $data['uf'] ?? null,
            'maritalStatus' => $data['marital_status'] ?? null,
            'spouse' => $data['spouse'] ?? null,
            'father' => $data['father'] ?? null,
            'mother' => $data['mother'] ?? null,
            'ecclesiasticalFunction' => $data['ecclesiastical_function'] ?? null,
            'memberType' => $data['member_type'] ?? null,
            'baptismDate' => $data['baptism_date'] ?? null,
            'bloodType' => $data['blood_type'] ?? null,
            'education' => $data['education'] ?? null,
            'groupIds' => isset($data['group_ids']) ? (is_string($data['group_ids']) ? json_decode($data['group_ids'], true) : $data['group_ids']) : null,
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        $prefixedData = self::getMemberPrefixedData($data);
        $nonPrefixedData = self::getNonPrefixedData($data);

        $mergedData = array_merge(
            ['id' => $data['members_id'] ?? $data['id'] ?? 0],
            array_filter($prefixedData, fn ($value) => $value !== null) ?:
                array_filter($nonPrefixedData, fn ($value) => $value !== null)
        );

        return new self($mergedData);
    }
}
