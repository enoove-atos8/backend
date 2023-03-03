<?php

namespace Domain\Patients\Actions;

use Domain\Patients\Models\Patient;
use Infrastructure\Traits\Roles\HasAuthorization;


class ListPatientAction
{
    use HasAuthorization;

    private Patient $patient;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function __invoke($id = null)
    {
        if($this->hasRole(auth()->user(), ['admin', 'receptionist', 'doctor']))
        {
            if ($id)
                return $this->patient->find($id);
            else
                return $this->patient->all();
            //return Patient::paginate();
        }
        else
        {
            return ["data"  =>  false,"status"  =>  403];
        }
    }
}
