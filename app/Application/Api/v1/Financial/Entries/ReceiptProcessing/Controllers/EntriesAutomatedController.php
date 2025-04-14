<?php

namespace Application\Api\v1\Financial\Entries\Automation\Controllers;

use Application\Api\v1\Financial\Entries\Automation\Resources\EntriesAutomatedResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Entries\Automation\Actions\DeleteEntryAutomation;
use Domain\Financial\Entries\Automation\Actions\GetEntriesAutomated;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Illuminate\Http\Request;

class EntriesAutomatedController extends Controller
{

    /**
     * @param Request $request
     * @param GetEntriesAutomated $getEntriesAutomated
     * @return EntriesAutomatedResourceCollection
     * @throws GeneralExceptions
     */
    public function getReadingErrorReceipts(Request $request, GetEntriesAutomated $getEntriesAutomated): EntriesAutomatedResourceCollection
    {
        try
        {
            $reason = strtoupper($request->input('reason'));
            $receipts = $getEntriesAutomated->execute($reason);

            return new EntriesAutomatedResourceCollection($receipts);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param DeleteEntryAutomation $deleteEntryAutomation
     * @return bool
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function deleteReadingErrorReceipt(Request $request, DeleteEntryAutomation $deleteEntryAutomation): bool
    {
        try
        {
            $id = $request->input('id');
            $deleted = $deleteEntryAutomation->execute($id);

            if($deleted)
                return true;

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
