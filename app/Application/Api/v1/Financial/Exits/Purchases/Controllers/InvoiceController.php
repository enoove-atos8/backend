<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Controllers;

use Application\Api\v1\Financial\Exits\Purchases\Resources\InvoicesResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Exits\Purchases\Actions\GetInvoiceIndicatorsAction;
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
            $getClosedInvoices = $request->boolean('getClosedInvoices');

            $invoices = $getClosedInvoices
                ? $getInvoicesByCardIdAction->execute($cardId, $getClosedInvoices)
                : $getInvoicesByCardIdAction->execute($cardId);

            return new InvoicesResourceCollection($invoices);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }




    /**
     **
     * @throws GeneralExceptions
     */
    public function getInvoiceIndicators(Request $request, GetInvoiceIndicatorsAction $getInvoiceIndicatorsAction): array
    {
        try
        {
            $cardId = $request->get('cardId');
            $invoiceId = $request->get('invoiceId');
            $indicators = $getInvoiceIndicatorsAction->execute($cardId, $invoiceId);

            return [
                'indicators'    =>  $indicators
            ];
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
