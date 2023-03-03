<?php

namespace App\Application\Api\Employees\Controllers;

use Application\Api\Employees\Requests\EmployeeRequest;
use Application\Api\Employees\Resources\ErrorEmployeeResource;
use Application\Api\Employees\Resources\EmployeeResource;
use Application\Api\Employees\Resources\EmployeeResourceCollection;
use Domain\Employees\Actions\CreateEmployeeAction;
use Application\Core\Http\Controllers\Controller;
use Domain\Employees\Actions\UpdateEmployeeAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class EmployeeController extends Controller
{
    public function index()
    {

    }

    public function show()
    {

    }

    /**
     * @param EmployeeRequest $employeeRequest
     * @param CreateEmployeeAction $createEmployeeAction
     * @return EmployeeResource
     * @throws UnknownProperties
     */
    public function store(EmployeeRequest $employeeRequest, CreateEmployeeAction $createEmployeeAction): EmployeeResource
    {
        $response = $createEmployeeAction($employeeRequest->employeeData());
        return new EmployeeResource($response);
    }

    /**
     * @param EmployeeRequest $employeeRequest
     * @param UpdateEmployeeAction $updateEmployeeAction
     * @param int $id
     * @return EmployeeResource
     * @throws UnknownProperties
     */
    public function update(EmployeeRequest $employeeRequest, UpdateEmployeeAction $updateEmployeeAction, int $id): EmployeeResource
    {
        $response = $updateEmployeeAction($employeeRequest->employeeData());
        return new EmployeeResource($response);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        //
    }
}
