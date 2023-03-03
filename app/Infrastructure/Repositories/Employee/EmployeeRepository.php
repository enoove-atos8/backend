<?php

namespace Infrastructure\Repositories\Employee;

use Domain\Employees\DataTransferObjects\EmployeeData;
use Domain\Employees\Interfaces\EmployeeRepositoryInterface;
use Domain\Employees\Models\Employee;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{

    /**
     * UserRepository constructor.
     *
     * @param Employee $model
     */
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection
     */
    public function getAllEmployees(): Collection
    {
        // TODO: Implement getAllEmployees() method.
    }

    /**
     * @param $id
     * @return Employee
     */
    public function getEmployeeById($id): Employee
    {
        // TODO: Implement getEmployeeById() method.
    }

    /**
     * @param EmployeeData $employeeData
     * @param $userId
     * @return Employee
     */
    public function createEmployee(EmployeeData $employeeData, $userId): Employee
    {
        return $this->model->create([
            'user_id'       =>  $userId,
            'first_name'    =>  $employeeData->firstName,
            'last_name'     =>  $employeeData->lastName,
            'gender'        =>  $employeeData->gender,
            'birth_date'    =>  $employeeData->birthDate,
            'cpf'           =>  $employeeData->cpf,
            'rg'            =>  $employeeData->rg,
            'cell_phone'    =>  $employeeData->cellPhone,
        ]);
    }

    /**
     * @param EmployeeData $employeeData
     * @param $id
     * @return bool
     */
    public function updateEmployee(EmployeeData $employeeData, $id): bool
    {
        // TODO: Implement updateEmployee() method.
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteEmployee($id): bool
    {
        // TODO: Implement deleteEmployee() method.
    }
}
