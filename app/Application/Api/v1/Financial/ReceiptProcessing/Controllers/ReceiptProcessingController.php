<?php

namespace Application\Api\v1\Financial\ReceiptProcessing\Controllers;

use Application\Api\v1\Financial\ReceiptProcessing\Resources\ReceiptsProcessingErrorResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\ReceiptProcessing\Actions\DeleteReceiptProcessedAction;
use Domain\Financial\ReceiptProcessing\Actions\GetNotProcessedReceiptsAction;
use Domain\Financial\ReceiptProcessing\Constants\ReturnMessages;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class ReceiptProcessingController extends Controller
{
    /**
     * @throws GeneralExceptions
     */
    public function getReceiptsProcessing(Request $request, GetNotProcessedReceiptsAction $getNotProcessedReceiptsAction): ReceiptsProcessingErrorResourceCollection
    {
        try
        {
            $status = $request->input('status');
            $docType = $request->input('docType');
            $receipts = $getNotProcessedReceiptsAction->execute($docType, $status);

            return new ReceiptsProcessingErrorResourceCollection($receipts);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int)$e->getCode(), $e);
        }
    }



    /**
     * @throws GeneralExceptions
     */
    public function deleteReceiptsProcessing($id, DeleteReceiptProcessedAction $deleteReceiptProcessedAction): ResponseFactory|Application|Response
    {
        try
        {
            $receiptDeleted = $deleteReceiptProcessedAction->execute($id);

            if($receiptDeleted)
            {
                return response([
                    'message'   =>  ReturnMessages::DELETE_SUCCESS,
                ], 200);
            }
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
