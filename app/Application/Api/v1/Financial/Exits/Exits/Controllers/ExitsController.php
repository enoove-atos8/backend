<?php

namespace Application\Api\v1\Financial\Exits\Exits\Controllers;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use Application\Api\v1\Financial\Entries\Entries\Resources\EntryResourceCollection;
use Application\Api\v1\Financial\Exits\Exits\Resources\ExitsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class ExitsController extends Controller
{
    /**
     * @param Request $request
     * @param GetExitsAction $getExitsAction
     * @param GetAllGroupsAction $getAllGroupsAction
     * @return ExitsResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getExits(Request $request, GetExitsAction $getExitsAction, GetAllGroupsAction $getAllGroupsAction): ExitsResourceCollection
    {
        try
        {
            $dates = $request->input('dates');
            $filters = $request->except(['dates','page']);
            $exits = $getExitsAction->execute($dates, $filters);

            return new ExitsResourceCollection($exits);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
