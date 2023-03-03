<?php

namespace Application\Api\Patients\Controllers;

use App\Domain\Patients\Actions\CreatePatientAction;
use Application\Api\Patients\Requests\PatientRequest;
use Application\Api\Patients\Resources\ErrorPatientResource;
use Application\Api\Patients\Resources\PatientResource;
use Application\Api\Patients\Resources\PatientResourceCollection;
use Domain\Patients\Actions\ListPatientAction;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ListPatientAction $listPatientAction
     * @return PatientResourceCollection|JsonResponse
     */
    public function index(ListPatientAction $listPatientAction): PatientResourceCollection|JsonResponse
    {
        $response = $listPatientAction();

        if (is_array($response))
            return (new ErrorPatientResource($response))->response()->setStatusCode($response["status"]);
        else
            return new PatientResourceCollection($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param PatientRequest $patientRequest
     * @param CreatePatientAction $createPatientAction
     * @return PatientResource
     * @throws UnknownProperties
     */
    public function create(PatientRequest $patientRequest, CreatePatientAction $createPatientAction): PatientResource
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PatientRequest $patientRequest
     * @param CreatePatientAction $createPatientAction
     * @return PatientResourceCollection|JsonResponse
     * @throws UnknownProperties
     */
    public function store(PatientRequest $patientRequest, CreatePatientAction $createPatientAction): PatientResourceCollection|JsonResponse
    {

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param ListPatientAction $listPatientAction
     * @return PatientResource|JsonResponse
     */
    public function show(int $id, ListPatientAction $listPatientAction): PatientResource|JsonResponse
    {
        $response = $listPatientAction($id);

        if (is_array($response))
            return (new ErrorPatientResource($response))->response()->setStatusCode($response["status"]);
        else
            return new PatientResource($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
