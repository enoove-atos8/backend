<?php

namespace Application\Api\v1\Ecclesiastical\Divisions\Controllers;

use Application\Api\v1\Ecclesiastical\Divisions\Requests\DivisionRequest;
use Application\Api\v1\Ecclesiastical\Divisions\Resources\DivisionsResourceCollection;
use Application\Api\v1\Ecclesiastical\Groups\Resources\GroupResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Divisions\Actions\CreateNewDivisionAction;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionIdByName;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionsAction;
use Domain\Ecclesiastical\Divisions\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\Actions\GetGroupsByDivisionAction;
use Domain\Ecclesiastical\Groups\Models\Group;
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
            $createNewDivisionAction($divisionRequest->divisionData());

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
            $response = $getDivisionsAction((int) $enabled);

            return new DivisionsResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetDivisionIdByName $getDivisionIdByName
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getDivisionIdByName(Request $request, GetDivisionIdByName $getDivisionIdByName): ResponseFactory|Application|Response
    {
        try
        {
            $division = $request->input('division');
            $response = $getDivisionIdByName($division);

            return response([
                'division'   =>  [
                    'id'    =>  $response
                ]
            ], 201);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
