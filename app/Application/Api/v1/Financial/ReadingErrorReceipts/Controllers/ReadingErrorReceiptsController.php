<?php

namespace Application\Api\v1\Financial\ReadingErrorReceipts\Controllers;

use Application\Api\v1\Financial\ReadingErrorReceipts\Resources\ReadingErrorReceiptsResourceCollection;
use Application\Core\Http\Controllers\Controller;
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
     * @return ReadingErrorReceiptsResourceCollection|ResponseFactory|Application|Response
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function getReadingErrorReceipts(Request $request, GetReadingErrorReceiptsAction $readingErrorReceiptsAction): ReadingErrorReceiptsResourceCollection|ResponseFactory|Application|Response
    {
        try
        {
            $reason = strtoupper($request->input('reason'));
            $receipts = $readingErrorReceiptsAction($reason);

            if(!is_null($receipts))
                return new ReadingErrorReceiptsResourceCollection($receipts);
            else
                return response(['message' => '', 404]);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
