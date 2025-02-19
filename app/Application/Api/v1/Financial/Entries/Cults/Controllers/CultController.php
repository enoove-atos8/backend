<?php

namespace Application\Api\v1\Financial\Entries\Cults\Controllers;


use App\Domain\Financial\Entries\Cults\Constants\ReturnMessages;
use Application\Api\v1\Financial\Entries\Cults\Requests\CultRequest;
use Application\Api\v1\Financial\Entries\Cults\Resources\CultResource;
use Application\Api\v1\Financial\Entries\Cults\Resources\CultsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Entries\Cults\Actions\GetCultsAction;
use Domain\Financial\Entries\Cults\Actions\GetDataCultByIdAction;
use Domain\Financial\Entries\Cults\Actions\SaveCultAction;
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
            $response = $getCultsAction->execute();

            return new CultsResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @throws Throwable
     */
    public function getCultById(Request $request, GetDataCultByIdAction $getDataCultByIdAction): CultResource
    {
        try
        {
            $id = $request->input('id');
            $response = $getDataCultByIdAction->execute($id);

            return new CultResource($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * Create new cult
     * @param CultRequest $cultRequest
     * @param $id
     * @param SaveCultAction $saveCultAction
     * @return Application|ResponseFactory|Response
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function saveCult(CultRequest $cultRequest, SaveCultAction $saveCultAction, $id = null): Application|ResponseFactory|Response
    {
        try
        {
            $saveCultAction->execute($id, $cultRequest->cultData(), $cultRequest->consolidationEntriesData());

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
