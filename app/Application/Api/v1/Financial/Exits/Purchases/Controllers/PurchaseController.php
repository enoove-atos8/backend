<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Controllers;

use App\Application\Api\v1\Financial\Exits\Purchases\Resources\CardsResourceCollection;
use Application\Api\v1\Financial\Exits\Purchases\Resources\PurchaseResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardsAction;
use Domain\Financial\Exits\Purchases\Actions\GetPurchasesAction;
use Exception;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;

class PurchaseController extends Controller
{
    /**
     **
     * @throws GeneralExceptions
     */
    public function getPurchases(Request $request, GetPurchasesAction $getPurchasesAction): PurchaseResourceCollection
    {
        try
        {
            $cardId = $request->get('cardId');
            $purchaseDate = $request->get('purchaseDate');
            $purchases = $getPurchasesAction->execute($cardId, $purchaseDate);

            return new PurchaseResourceCollection($purchases);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
