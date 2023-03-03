<?php

namespace Domain\Patients\Interfaces;

use Domain\Patients\DataTransferObjects\PatientData;
use Illuminate\Support\Collection;
use Domain\Patients\Models\Patient;

interface PatientRepositoryInterface
{
    public function all(): Collection;

    public function createPatient(PatientData $patientData): Patient;
}

