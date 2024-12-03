<?php

namespace Application\Api\v1\Financial\Entries\Automation\Controllers;

use Application\Api\v1\Financial\Entries\Automation\Resources\ReadingErrorReceiptsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Receipts\Entries\ReadingError\Actions\DeleteReadingErrorReceiptAction;
use Domain\Financial\Receipts\Entries\ReadingError\Actions\GetReadingErrorReceiptsAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Illuminate\Http\Request;

class ReadingErrorReceiptsController extends Controller
{

    /**
     * @param Request $request
     * @param GetReadingErrorReceiptsAction $readingErrorReceiptsAction
     * @return ReadingErrorReceiptsResourceCollection
     * @throws GeneralExceptions
     */
    public function getReadingErrorReceipts(Request $request, GetReadingErrorReceiptsAction $readingErrorReceiptsAction): ReadingErrorReceiptsResourceCollection
    {
        try
        {
            $reason = strtoupper($request->input('reason'));
            $receipts = $readingErrorReceiptsAction($reason);

            return new ReadingErrorReceiptsResourceCollection($receipts);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param DeleteReadingErrorReceiptAction $deleteReadingErrorReceiptAction
     * @return bool
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function deleteReadingErrorReceipt(Request $request, DeleteReadingErrorReceiptAction $deleteReadingErrorReceiptAction): bool
    {
        try
        {
            $id = $request->input('id');
            $deleted = $deleteReadingErrorReceiptAction($id);

            if($deleted)
                return true;

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
