<?php

namespace Application\Api\v1\Entry\Controllers\Consolidated;

use Application\Api\v1\Entry\Resources\EntriesEvolutionConsolidationResourceCollection;
use Application\Api\v1\Entry\Resources\EntryConsolidatedResourceCollection;
use Application\Api\v1\Entry\Resources\EntryResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Entries\Consolidated\Actions\GetEntriesEvolutionConsolidatedAction;
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

            $response = $getEntriesEvolutionConsolidatedAction($limit);
            return new EntriesEvolutionConsolidationResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
