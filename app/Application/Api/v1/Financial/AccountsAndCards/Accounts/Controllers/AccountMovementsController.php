<?php

namespace App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers;

use App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Resources\Movements\MovementsResourcesCollection;
use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Movements\GetMovementsAction;
use Exception;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;

class AccountMovementsController
{
    /**
     * Get movements by account id and reference date
     *
     * @param Request $request
     * @param GetMovementsAction $getMovementsAction
     * @return MovementsResourcesCollection
     * @throws GeneralExceptions
     */
    public function getMovements(Request $request, GetMovementsAction $getMovementsAction): MovementsResourcesCollection
    {
        try
        {
            $accountId = $request->input('accountId');
            $referenceDate = $request->input('referenceDate');

            $movements = $getMovementsAction->execute($accountId, $referenceDate);

            return new MovementsResourcesCollection($movements);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
