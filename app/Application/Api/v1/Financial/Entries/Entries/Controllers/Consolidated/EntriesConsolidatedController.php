<?php

namespace Application\Api\v1\Financial\Entries\Entries\Controllers\Consolidated;

use App\Domain\Financial\Entries\Consolidation\Actions\GetEntriesEvolutionConsolidatedAction;
use Application\Api\v1\Financial\Entries\Entries\Resources\EntriesEvolutionConsolidationResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class EntriesConsolidatedController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getEntriesEvolution
    (
        Request $request,
        GetEntriesEvolutionConsolidatedAction $getEntriesEvolutionConsolidatedAction
    ): EntriesEvolutionConsolidationResourceCollection
    {
        try
        {
            $limit = $request->input('limit');

            $response = $getEntriesEvolutionConsolidatedAction->execute(1, $limit);
            return new EntriesEvolutionConsolidationResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
