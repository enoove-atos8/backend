<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Controllers;

use Application\Api\v1\Financial\Exits\Purchases\Resources\InvoicesResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Exits\Purchases\Actions\GetInvoicesByCardIdAction;
use Exception;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;

class InvoiceController extends Controller
{
    /**
    **
    * @throws GeneralExceptions
    */
    public function getInvoicesByCardId(Request $request, GetInvoicesByCardIdAction $getInvoicesByCardIdAction): InvoicesResourceCollection
    {
        try
        {
            $cardId = $request->get('cardId');
            $invoices = $getInvoicesByCardIdAction->execute($cardId);

            return new InvoicesResourceCollection($invoices);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
