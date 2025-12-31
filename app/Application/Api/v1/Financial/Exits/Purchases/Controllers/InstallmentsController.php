<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Controllers;

use Application\Api\v1\Financial\Exits\Purchases\Resources\InstallmentsByPurchaseResourceCollection;
use Application\Api\v1\Financial\Exits\Purchases\Resources\InstallmentsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Exits\Purchases\Actions\GetInstallmentsAction;
use Domain\Financial\Exits\Purchases\Actions\GetInstallmentsByPurchaseAction;
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
        try {
            $cardId = $request->get('cardId');
            $date = $request->get('date');
            $installments = $getInstallmentsAction->execute($cardId, $date);

            return new InstallmentsResourceCollection($installments);
        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), 500, $e);
        }
    }

    /**
     * @throws GeneralExceptions
     */
    public function getInstallmentsByPurchase(Request $request, GetInstallmentsByPurchaseAction $getInstallmentsByPurchaseAction): InstallmentsByPurchaseResourceCollection
    {
        try {
            $purchaseId = $request->get('purchaseId');
            $result = $getInstallmentsByPurchaseAction->execute($purchaseId);

            return new InstallmentsByPurchaseResourceCollection($result);
        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), 500, $e);
        }
    }
}
