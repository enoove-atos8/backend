<?php

namespace Application\Api\v1\Financial\Entries\Entries\Controllers\Consolidated;

use App\Domain\Financial\Entries\Consolidation\Actions\GetEntriesEvolutionConsolidatedAction;
use Application\Api\v1\Financial\Entries\Entries\Requests\EntriesEvolutionRequest;
use Application\Api\v1\Financial\Entries\Entries\Resources\EntriesEvolutionConsolidationResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class EntriesConsolidatedController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getEntriesEvolution(
        EntriesEvolutionRequest $request,
        GetEntriesEvolutionConsolidatedAction $getEntriesEvolutionConsolidatedAction
    ): EntriesEvolutionConsolidationResourceCollection {
        try {
            $response = $getEntriesEvolutionConsolidatedAction->execute(1, $request->getLimit());

            return new EntriesEvolutionConsolidationResourceCollection($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
