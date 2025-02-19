<?php

namespace App\Application\Api\v1\Financial\Reviewer\Controllers;

use App\Application\Api\v1\Financial\Reviewer\Resources\FinancialReviewerResourceCollection;
use App\Domain\Financial\Reviewers\Actions\GetFinancialReviewersAction;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        try
        {
            $response = $getFinancialReviewersAction->execute();
            return new FinancialReviewerResourceCollection($response);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
