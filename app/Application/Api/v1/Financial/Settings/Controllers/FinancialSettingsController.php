<?php

namespace App\Application\Api\v1\Financial\Settings\Controllers;

use App\Application\Api\v1\Financial\Settings\Requests\FinancialSettingsRequest;
use App\Application\Api\v1\Financial\Settings\Resources\FinancialSettingsResource;
use App\Domain\Financial\Settings\Actions\GetFinancialSettingsAction;
use App\Domain\Financial\Settings\Actions\SaveFinancialSettingsAction;
use App\Domain\Financial\Settings\Constants\ReturnMessages;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Settings\FinancialSettingsRepository;
use Throwable;

class FinancialSettingsController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getSettings(
        Request $request,
        GetFinancialSettingsAction $getFinancialSettingsAction
    ): FinancialSettingsResource|JsonResponse {
        try {
            $budgetType = $request->query('budgetType', FinancialSettingsRepository::BUDGET_TYPE_TITHES);
            $response = $getFinancialSettingsAction->execute($budgetType);

            if ($response === null) {
                return response()->json([
                    'data' => null,
                    'message' => ReturnMessages::SETTINGS_INFO_NOT_FOUND,
                ], 200);
            }

            return new FinancialSettingsResource($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function saveSettings(
        FinancialSettingsRequest $request,
        SaveFinancialSettingsAction $saveFinancialSettingsAction
    ): Response {
        try {
            $saveFinancialSettingsAction->execute($request->toData());

            return response([
                'message' => ReturnMessages::SETTINGS_SUCCESS_SAVED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
