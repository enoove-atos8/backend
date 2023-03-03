<?php

namespace Domain\Patients\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PatientData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var integer  */
    public int $userId = 0;

    /** @var integer  */
    public int $patientResponsibleId = 0;

    /** @var string  */
    public string $firstName;

    /** @var string  */
    public string $lastName;

    /** @var string  */
    public string $birth_date;

    /** @var string  */
    public string $cpf;

    /** @var string  */
    public string $rg;

    /** @var string  */
    public string $cell_phone;


}
