<?php

namespace Domain\Employees\Actions;

use App\Infrastructure\Repositories\Employee\EmployeeRepository;
use Domain\Employees\DataTransferObjects\EmployeeData;
use Domain\Employees\Interfaces\EmployeeRepositoryInterface;
use Domain\Employees\Models\Employee;

class UpdateEmployeeAction
{
    private EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * @param EmployeeData $employeeData
     * @return Employee
     */
    public function __invoke(EmployeeData $employeeData): Employee
    {
        $employee = $this->employeeRepository->createEmployee($employeeData);

        return $employee;
    }
}
