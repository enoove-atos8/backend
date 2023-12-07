<?php

namespace Domain\Members\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class MemberData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var int  */
    public int $activated;

    /** @var int  */
    public int $deleted;

    /** @var string|null  */
    public string|null $avatar;

    /** @var string  */
    public string $fullName;

    /** @var string  */
    public string $gender;

    /** @var string  */
    public string $cpf;

    /** @var string  */
    public string $rg;

    /** @var string  */
    public string $work;

    /** @var string  */
    public string $bornDate;

    /** @var string  */
    public string $email;

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

    /** @var string  */
    public string $maritalStatus;

    /** @var string  */
    public string $spouse;

    /** @var string  */
    public string $father;

    /** @var string  */
    public string $mother;

    /** @var array|null  */
    public array|null $ecclesiasticalFunction;

    /** @var array|null  */
    public array|null $ministries;

    /** @var string  */
    public string $baptismDate;

    /** @var string  */
    public string $bloodType;

    /** @var string  */
    public string $education;
}

