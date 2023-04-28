<?php

namespace Domain\Persons\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PersonData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

/** @var integer  */
    public int $userId = 0;

    /** @var string  */
    public string $firstName;

    /** @var string  */
    public string $lastName;

    /** @var string  */
    public string $avatar;

    /** @var string  */
    public string $gender;

    /** @var string */
    public string $birthDate;

    /** @var string  */
    public string $cpf;

    /** @var string  */
    public string $rg;

    /** @var string  */
    public string $cellPhone;

    /** @var string  */
    public string $ministry;

    /** @var string  */
    public string $department;

    /** @var string  */
    public string $responsibility;



}
