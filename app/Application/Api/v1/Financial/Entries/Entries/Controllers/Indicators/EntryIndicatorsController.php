<?php

namespace Application\Api\v1\Financial\Entries\Entries\Controllers\Indicators;


use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Entries\Indicators\Actions\HandleEntriesIndicatorsAction;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class EntryIndicatorsController extends Controller
{


    /**
     * @param Request $request
     * @param HandleEntriesIndicatorsAction $handleEntriesIndicatorsAction
     * @return array
     * @throws GeneralExceptions|Throwable
     */
    public function getEntriesIndicators(Request $request, HandleEntriesIndicatorsAction $handleEntriesIndicatorsAction): array
    {
        try
        {
            $indicator = $request->input('indicator');
            $dates = $request->input('dates');
            $filters = $request->except(['dates', 'page', 'indicator']);
            return $handleEntriesIndicatorsAction->execute($indicator, $dates, $filters);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
