<?php

namespace Domain\Employees\Actions;

use Infrastructure\Repositories\Employee\EmployeeRepository;
use Infrastructure\Repositories\User\UserRepository;
use Domain\Employees\Interfaces\EmployeeRepositoryInterface;
use Domain\Users\Interfaces\UserRepositoryInterface;

class CreateEmployeeAction
{
    private EmployeeRepository $employeeRepository;
    private UserRepository $userRepository;
    private const ARR_USER_DATA_NAME = 'userData';
    private const ARR_EMPLOYEE_DATA_NAME = 'employeeData';

    public function __construct(UserRepositoryInterface $userRepository, EmployeeRepositoryInterface $employeeRepository)
    {
        $this->userRepository = $userRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function __invoke($modelData)
    {
        $user = $this->userRepository->createUser($modelData[self::ARR_USER_DATA_NAME]);
        $employee = $this->employeeRepository->createEmployee($modelData[self::ARR_EMPLOYEE_DATA_NAME], $user->id);

        if ($modelData[self::ARR_USER_DATA_NAME]->roles != null){

            $this->userRepository->attachRoles($modelData[self::ARR_USER_DATA_NAME], $user);

            if ($modelData[self::ARR_USER_DATA_NAME]->roles['abilities'] != null){

                $this->userRepository->attachAbilities($modelData[self::ARR_USER_DATA_NAME], $user);
            }
        }

        return [
            'user'      =>  $user,
            'employee'  =>  $employee
        ];
    }
}



