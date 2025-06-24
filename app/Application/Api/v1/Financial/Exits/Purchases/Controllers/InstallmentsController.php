<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Controllers;

use Application\Api\v1\Financial\Exits\Purchases\Resources\InstallmentsResourceCollection;
use Application\Api\v1\Financial\Exits\Purchases\Resources\PurchaseResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Exits\Purchases\Actions\GetInstallmentsAction;
use Domain\Financial\Exits\Purchases\Actions\GetPurchasesAction;
use Exception;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;

class InstallmentsController extends Controller
{
    /**
     **
     * @throws GeneralExceptions
     */
    public function getInstalments(Request $request, GetInstallmentsAction $getInstallmentsAction): InstallmentsResourceCollection
    {
        try
        {
            $cardId = $request->get('cardId');
            $date = $request->get('date');
            $purchases = $getInstallmentsAction->execute($cardId, $date);

            return new InstallmentsResourceCollection($purchases);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), 500, $e);
        }
    }
}
