<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Controllers;

use App\Domain\Financial\Exits\Purchases\Actions\DeletePurchaseAction;
use Application\Api\v1\Financial\Exits\Purchases\Resources\PurchaseResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Exits\Purchases\Actions\GetPurchasesAction;
use Domain\Financial\Exits\Purchases\Actions\PostponePurchaseAction;
use Domain\Financial\Exits\Purchases\Constants\ReturnMessages;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class PurchaseController extends Controller
{
    /**
     **
     * @throws GeneralExceptions
     */
    public function getPurchases(Request $request, GetPurchasesAction $getPurchasesAction): PurchaseResourceCollection
    {
        try {
            $cardId = $request->get('cardId');
            $purchaseDate = $request->get('purchaseDate');
            $purchases = $getPurchasesAction->execute($cardId, $purchaseDate);

            return new PurchaseResourceCollection($purchases);
        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Delete a purchase
     *
     * @throws GeneralExceptions|Throwable
     */
    public function deletePurchase(int $id, DeletePurchaseAction $deletePurchaseAction): Application|ResponseFactory|Response
    {
        try {
            $purchaseDeleted = $deletePurchaseAction->execute($id);

            if ($purchaseDeleted) {
                return response([
                    'message' => ReturnMessages::PURCHASE_DELETED,
                ], 200);
            }

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Postpone a purchase to the next invoice
     *
     * @throws GeneralExceptions
     */
    public function postponePurchase(int $id, PostponePurchaseAction $postponePurchaseAction): Application|ResponseFactory|Response
    {
        try {
            $postponed = $postponePurchaseAction->execute($id);

            if ($postponed) {
                return response([
                    'message' => ReturnMessages::PURCHASE_POSTPONED,
                ], 200);
            }

            return response([
                'message' => ReturnMessages::PURCHASE_POSTPONE_ERROR,
            ], 500);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
