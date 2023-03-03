<?php

namespace Domain\Employees\Interfaces;

use Domain\Employees\DataTransferObjects\EmployeeData;
use Domain\Employees\Models\Employee;
use Illuminate\Support\Collection;

interface EmployeeRepositoryInterface
{
    public function getAllEmployees():Collection;

    public function getEmployeeById($id): Employee;

    public function createEmployee(EmployeeData $employeeData, $userId): Employee;

    public function updateEmployee(EmployeeData $employeeData, $id): bool;

    public function deleteEmployee($id): bool;
}
