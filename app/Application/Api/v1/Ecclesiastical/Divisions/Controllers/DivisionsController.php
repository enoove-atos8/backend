<?php

namespace Application\Api\v1\Ecclesiastical\Divisions\Controllers;

use Application\Api\v1\Ecclesiastical\Divisions\Requests\DivisionRequest;
use Application\Api\v1\Ecclesiastical\Divisions\Resources\DivisionsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Divisions\Actions\CreateNewDivisionAction;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionByNameAction;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionsAction;
use Domain\Ecclesiastical\Divisions\Constants\ReturnMessages;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class DivisionsController extends Controller
{
    /**
     *
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function createDivision(DivisionRequest $divisionRequest, CreateNewDivisionAction $createNewDivisionAction): Application|Response|ResponseFactory
    {
        try
        {
            $tenant = explode('.', $divisionRequest->getHost())[0];
            $createNewDivisionAction->execute($divisionRequest->divisionData(), $tenant);

            return response([
                'message'   =>  ReturnMessages::DIVISION_CREATED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }




    /**
     * @param Request $request
     * @param GetDivisionsAction $getDivisionsAction
     * @return DivisionsResourceCollection
     * @throws GeneralExceptions|Throwable
     */
    public function getDivisions(Request $request, GetDivisionsAction $getDivisionsAction): DivisionsResourceCollection
    {
        try
        {
            $enabled = $request->input('enabled');
            $response = $getDivisionsAction->execute((int) $enabled);

            return new DivisionsResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetDivisionByNameAction $getDivisionByName
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getDivisionIdByName(Request $request, GetDivisionByNameAction $getDivisionByName): ResponseFactory|Application|Response
    {
        try
        {
            $division = $request->input('division');
            $response = $getDivisionByName->execute($division);

            if (!$response) {
                return response([
                    'message' => 'Division not found'
                ], 404);
            }

            return response([
                'division'   =>  [
                    'id'    =>  $response->id
                ]
            ], 201);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @param Request $request
     * @param GetDivisionByNameAction $getDivisionByName
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getDivisionByName(Request $request, GetDivisionByNameAction $getDivisionByName): ResponseFactory|Application|Response
    {
        try
        {
            $division = $request->input('division');
            $response = $getDivisionByName->execute($division);

            return response([
                'division' => [
                    'id' => $response->id,
                    'name' => $response->name,
                    'slug' => $response->slug,
                ]
            ], 201);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
