<?php

namespace Application\Api\v1\Mobile\SyncStorage\Controllers;

use App\Domain\SyncStorage\Actions\NewReceiptToProcessAction;
use Application\Api\v1\Mobile\SyncStorage\Requests\ReceiptDataRequest;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class SyncStorageController extends Controller
{

    /**
     * @param ReceiptDataRequest $receiptDataRequest
     * @param NewReceiptToProcessAction $newReceiptToProcessAction
     * @return Application|ResponseFactory|Response
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function sendDataToServer(ReceiptDataRequest $receiptDataRequest, NewReceiptToProcessAction $newReceiptToProcessAction): Application|ResponseFactory|Response
    {
        try
        {
            $file = $receiptDataRequest->files->get('file');
            $newReceiptToProcessAction->execute($receiptDataRequest->syncStorageData(), $file);

            return response([
                'message'   =>  'SUCESSO!!!',
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
