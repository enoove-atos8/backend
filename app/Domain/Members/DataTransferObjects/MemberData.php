<?php

namespace Domain\Members\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MemberData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var bool | null  */
    public bool | null $activated;

    /** @var bool | null  */
    public bool | null $deleted;

    /** @var string | null  */
    public string | null $avatar;

    /** @var string | null  */
    public string | null $fullName;

    /** @var string | null  */
    public string | null $gender;

    /** @var string|null  */
    public string | null $cpf;

    /** @var string | null  */
    public string | null $middleCpf;

    /** @var string|null  */
    public string | null $rg;

    /** @var string|null  */
    public string | null $work;

    /** @var string | null  */
    public string | null $bornDate;

    /** @var string|null  */
    public string | null $email;

    /** @var string|null  */
    public string | null $phone;

    /** @var string | null  */
    public string | null $cellPhone;

    /** @var string | null  */
    public string | null $address;

    /** @var string | null  */
    public string | null $district;

    /** @var string | null  */
    public string | null $city;

    /** @var string | null  */
    public string | null $uf;

    /** @var string|null  */
    public string | null $maritalStatus;

    /** @var string|null  */
    public string | null $spouse;

    /** @var string|null  */
    public string | null $father;

    /** @var string | null  */
    public string | null $mother;

    /** @var array|string|null  */
    public array | string | null $ecclesiasticalFunction;

    /** @var string | null  */
    public string | null $memberType;

    /** @var string|null  */
    public string | null $baptismDate;

    /** @var string|null  */
    public string | null $bloodType;

    /** @var string|null  */
    public string | null $education;

    /**
     * Create a MemberData instance from response data
     *
     * @param array $data Response data from repository
     * @return array New MemberData instance
     */
    private static function getMemberPrefixedData(array $data): array
    {
        return [
            'activated' => isset($data['members_activated']) ? (bool)$data['members_activated'] : null,
            'deleted' => isset($data['members_deleted']) ? (bool)$data['members_deleted'] : null,
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
        ];
    }

    private static function getNonPrefixedData(array $data): array
    {
        return [
            'activated' => isset($data['activated']) ? (bool)$data['activated'] : null,
            'deleted' => isset($data['deleted']) ? (bool)$data['deleted'] : null,
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
            ['id' => $data['id'] ?? 0],
            array_filter($prefixedData, fn($value) => $value !== null) ?:
                array_filter($nonPrefixedData, fn($value) => $value !== null)
        );

        return new self($mergedData);
    }
}

