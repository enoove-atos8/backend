<?php

namespace App\Application\Api\v1\Financial\Reviewer\Controllers;

use App\Application\Api\v1\Financial\Reviewer\Requests\FinancialReviewerRequest;
use App\Application\Api\v1\Financial\Reviewer\Resources\FinancialReviewerResourceCollection;
use App\Domain\Financial\Reviewers\Actions\DeleteFinancialReviewerAction;
use App\Domain\Financial\Reviewers\Actions\GetFinancialReviewersAction;
use App\Domain\Financial\Reviewers\Actions\SaveFinancialReviewerAction;
use App\Domain\Financial\Reviewers\Constants\ReturnMessages;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class FinancialReviewerController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getFinancialReviewers(GetFinancialReviewersAction $getFinancialReviewersAction): FinancialReviewerResourceCollection
    {
        try {
            $response = $getFinancialReviewersAction->execute();

            return new FinancialReviewerResourceCollection($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function batchCreateReviewers(
        FinancialReviewerRequest $request,
        SaveFinancialReviewerAction $saveFinancialReviewerAction
    ): Response {
        try {
            $reviewersData = $request->financialReviewersData();
            $result = $saveFinancialReviewerAction->execute($reviewersData);

            if ($result) {
                return response([
                    'success' => true,
                ], 201);
            }

            return response([
                'success' => false,
                'message' => 'Erro ao cadastrar revisores',
            ], 500);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function deleteReviewer(
        int $id,
        DeleteFinancialReviewerAction $deleteFinancialReviewerAction
    ): Response {
        try {
            $result = $deleteFinancialReviewerAction->execute($id);

            if ($result) {
                return response([
                    'success' => true,
                ], 200);
            }

            return response([
                'success' => false,
                'message' => ReturnMessages::ERROR_REVIEWER_NOT_FOUND,
            ], 404);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
