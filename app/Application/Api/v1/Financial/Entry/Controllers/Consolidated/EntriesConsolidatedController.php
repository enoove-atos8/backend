<?php

namespace App\Application\Api\v1\Financial\Entry\Controllers\Consolidated;

use App\Application\Api\v1\Financial\Entry\Resources\EntriesEvolutionConsolidationResourceCollection;
use App\Domain\Financial\Entries\Consolidated\Actions\GetEntriesEvolutionConsolidatedAction;
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

            $response = $getEntriesEvolutionConsolidatedAction('*', $limit);
            return new EntriesEvolutionConsolidationResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
