<?php

namespace Infrastructure\Repositories\Patient;

use Domain\Patients\DataTransferObjects\PatientData;
use Domain\Patients\Interfaces\PatientRepositoryInterface;
use Domain\Patients\Models\Patient;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class PatientRepository extends BaseRepository implements PatientRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param Patient $model
     */
    public function __construct(Patient $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function createPatient(PatientData $patientData): Patient
    {
        return $this->model->create(Arr::except($patientData->toArray(),['']));
    }
}
