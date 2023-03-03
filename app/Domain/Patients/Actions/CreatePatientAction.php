<?php

namespace Domain\Patients\Actions;

use App\Infrastructure\Repositories\Patient\PatientRepository;
use Domain\Patients\DataTransferObjects\PatientData;
use Domain\Patients\Interfaces\PatientRepositoryInterface;
use Domain\Patients\Models\Patient;
use Infrastructure\Traits\Roles\HasAuthorization;

class CreatePatientAction
{
    use HasAuthorization;

    private PatientRepository $patientRepository;

    public function __construct(PatientRepositoryInterface $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    public function __invoke(PatientData $patientData): Patient
    {
        $patient = $this->patientRepository->createPatient($patientData);

        return $patient;
    }
}
