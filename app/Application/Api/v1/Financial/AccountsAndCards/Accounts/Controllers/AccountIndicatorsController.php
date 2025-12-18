<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers;

use Application\Core\Http\Controllers\Controller;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Indicators\GetAccountsIndicatorsAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Indicators\GetConciliationStatusAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Indicators\GetMonthSummaryAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Indicators\GetPendingFilesAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Indicators\GetRecentMovementsAction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;

class AccountIndicatorsController extends Controller
{
    /**
     * Get accounts indicators (balances and last movement date)
     *
     * @throws GeneralExceptions
     */
    public function getAccountsIndicators(GetAccountsIndicatorsAction $action): JsonResponse
    {
        try {
            $result = $action->execute();

            return response()->json($result);
        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get month summary (credits, debits, counts) grouped by account
     *
     * @throws GeneralExceptions
     */
    public function getMonthSummary(Request $request, GetMonthSummaryAction $action): JsonResponse
    {
        try {
            $referenceDate = $request->input('referenceDate', date('Y-m'));
            $result = $action->execute($referenceDate);

            return response()->json([
                'summaries' => $result,
            ]);
        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get conciliation status by account for a given month
     *
     * @throws GeneralExceptions
     */
    public function getConciliationStatus(Request $request, GetConciliationStatusAction $action): JsonResponse
    {
        try {
            $referenceDate = $request->input('referenceDate', date('Y-m'));
            $result = $action->execute($referenceDate);

            return response()->json([
                'statuses' => $result,
            ]);
        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get recent movements across all accounts
     *
     * @throws GeneralExceptions
     */
    public function getRecentMovements(Request $request, GetRecentMovementsAction $action): JsonResponse
    {
        try {
            $limit = (int) $request->input('limit', 30);
            $result = $action->execute($limit);

            return response()->json([
                'movements' => $result,
            ]);
        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get pending files
     *
     * @throws GeneralExceptions
     */
    public function getPendingFiles(GetPendingFilesAction $action): JsonResponse
    {
        try {
            $result = $action->execute();

            return response()->json([
                'files' => $result,
            ]);
        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
