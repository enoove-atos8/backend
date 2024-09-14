<?php

namespace Domain\Members\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

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

    /** @var array|null  */
    public array | null $ecclesiasticalFunction;

    /** @var string | null  */
    public string | null $memberType;

    /** @var string|null  */
    public string | null $baptismDate;

    /** @var string|null  */
    public string | null $bloodType;

    /** @var string|null  */
    public string | null $education;
}

