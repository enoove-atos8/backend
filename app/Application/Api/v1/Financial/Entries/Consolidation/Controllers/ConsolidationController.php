<?php

namespace Application\Api\v1\Financial\Entries\Consolidation\Controllers;


use App\Domain\Financial\Entries\Consolidation\Actions\UpdateAmountConsolidatedEntriesAction;
use App\Domain\Financial\Entries\Consolidation\Actions\UpdateStatusConsolidatedEntriesAction;
use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use Application\Api\v1\Financial\Entries\Consolidation\Resources\ConsolidationResourceCollection;
use Domain\Financial\Entries\Consolidation\Actions\ReopenConsolidatedMonthAction;
use Application\Api\v1\Financial\Entries\Cults\Requests\CultRequest;
use Application\Api\v1\Financial\Entries\Cults\Resources\CultsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Entries\Consolidation\Actions\GetMonthsAction;
use Domain\Financial\Entries\Cults\Actions\CreateCultAction;
use Domain\Financial\Entries\Cults\Actions\GetCultsAction;
use Domain\Financial\Entries\Entries\Actions\GetTotalAmountEntriesAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class ConsolidationController extends Controller
{
    /**
     * @throws Throwable
     * @throws GeneralExceptions
     */
    public function getMonths(GetMonthsAction $getMonthsAction): ConsolidationResourceCollection
    {
        try
        {
            $response = $getMonthsAction->execute();
            return new ConsolidationResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * Create new cult
     * @param Request $request
     * @param UpdateAmountConsolidatedEntriesAction $updateAmountConsolidatedEntriesAction
     * @param UpdateStatusConsolidatedEntriesAction $updateStatusConsolidationEntriesAction
     * @return Application|ResponseFactory|Response
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function consolidateMonth(Request $request, UpdateAmountConsolidatedEntriesAction $updateAmountConsolidatedEntriesAction, UpdateStatusConsolidatedEntriesAction $updateStatusConsolidationEntriesAction): Application|ResponseFactory|Response
    {
        try
        {
            $date = $request->input('date');
            $updateAmountConsolidatedEntriesAction->execute($date);
            $updateStatusConsolidationEntriesAction->execute($date, ConsolidationRepository::CONSOLIDATED_VALUE);

            return response([
                'message'   =>  ReturnMessages::SUCCESS_ENTRIES_CONSOLIDATED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * Reopen a consolidated month by resetting all amounts and status
     * @param Request $request
     * @param ReopenConsolidatedMonthAction $reopenConsolidatedMonthAction
     * @return Application|ResponseFactory|Response
     * @throws GeneralExceptions
     */
    public function reopenMonth(Request $request, ReopenConsolidatedMonthAction $reopenConsolidatedMonthAction): Application|ResponseFactory|Response
    {
        try
        {
            $date = $request->input('date');
            $reopenConsolidatedMonthAction->execute($date);

            return response([
                'message'   =>  ReturnMessages::SUCCESS_ENTRIES_CONSOLIDATED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getTotalAmountEntries(Request $request, GetTotalAmountEntriesAction $getTotalAmountEntriesAction): Application|Response|ResponseFactory
    {
        try
        {
            $date = $request->input('dates');
            $response = $getTotalAmountEntriesAction->execute($date);

            return response($response, 201);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
