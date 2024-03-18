<?php

namespace Domain\Members\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class MemberData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var bool  */
    public bool $activated;

    /** @var bool  */
    public bool $deleted;

    /** @var string|null  */
    public string|null $avatar;

    /** @var string  */
    public string $fullName;

    /** @var string  */
    public string $gender;

    /** @var string|null  */
    public string|null $cpf;

    /** @var string|null  */
    public string|null $rg;

    /** @var string|null  */
    public string|null $work;

    /** @var string  */
    public string $bornDate;

    /** @var string|null  */
    public string|null $email;

    /** @var string|null  */
    public string|null $phone;

    /** @var string  */
    public string $cellPhone;

    /** @var string  */
    public string $address;

    /** @var string  */
    public string $district;

    /** @var string  */
    public string $city;

    /** @var string  */
    public string $uf;

    /** @var string|null  */
    public string|null $maritalStatus;

    /** @var string|null  */
    public string|null $spouse;

    /** @var string|null  */
    public string|null $father;

    /** @var string  */
    public string $mother;

    /** @var array|null  */
    public array|null $ecclesiasticalFunction;

    /** @var string  */
    public string $memberType;

    /** @var array|null  */
    public array|null $ministries;

    /** @var string|null  */
    public string|null $baptismDate;

    /** @var string|null  */
    public string|null $bloodType;

    /** @var string|null  */
    public string|null $education;
}

