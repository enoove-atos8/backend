<?php

namespace Application\Api\v1\Financial\Entries\Cults\Controllers;


use App\Domain\Financial\Entries\Cults\Constants\ReturnMessages;
use Application\Api\v1\Financial\Entries\Cults\Requests\CultRequest;
use Application\Api\v1\Financial\Entries\Cults\Resources\CultsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Entries\Cults\Actions\CreateCultAction;
use Domain\Financial\Entries\Cults\Actions\GetCultsAction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class CultController extends Controller
{
    /**
     * @throws Throwable
     */
    public function getCults(Request $request, GetCultsAction $getCultsAction): CultsResourceCollection
    {
        try
        {
            $response = $getCultsAction();

            return new CultsResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * Create new cult
     * @param \Application\Api\v1\Financial\Entries\Cults\Requests\CultRequest $cultRequest
     * @param CreateCultAction $createCultAction
     * @return Application|ResponseFactory|Response
     * @throws GeneralExceptions
     * @throws UnknownProperties|Throwable
     */
    public function createCult(CultRequest $cultRequest, CreateCultAction $createCultAction): Application|ResponseFactory|Response
    {
        try
        {
            $createCultAction($cultRequest->cultData(), $cultRequest->consolidationEntriesData());

            return response([
                'message'   =>  ReturnMessages::SUCCESS_CULT_REGISTERED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
