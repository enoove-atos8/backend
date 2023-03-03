<?php

namespace Domain\Employees\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class EmployeeData extends DataTransferObject
{
    /** @var string  */
    public string $firstName;

    /** @var string  */
    public string $lastName;

    /** @var string  */
    public string $gender;

    /** @var string  */
    public string $birthDate;

    /** @var string  */
    public string $cpf;

    /** @var string  */
    public string $rg;

    /** @var string  */
    public string $cellPhone;
}
