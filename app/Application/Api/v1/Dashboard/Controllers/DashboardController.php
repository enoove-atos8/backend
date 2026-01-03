<?php

namespace App\Application\Api\v1\Dashboard\Controllers;

use App\Application\Api\v1\Dashboard\Requests\EntriesVsExitsRequest;
use App\Application\Api\v1\Dashboard\Requests\MemberEngagementRequest;
use App\Application\Api\v1\Dashboard\Resources\DashboardOverviewResource;
use App\Application\Api\v1\Dashboard\Resources\EntriesVsExitsResource;
use App\Application\Api\v1\Dashboard\Resources\MemberEngagementResource;
use App\Domain\Dashboard\Actions\GetDashboardOverviewAction;
use App\Domain\Dashboard\Actions\GetEntriesVsExitsAction;
use App\Domain\Dashboard\Actions\GetMemberEngagementAction;
use Application\Core\Http\Controllers\Controller;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class DashboardController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getOverview(
        GetDashboardOverviewAction $action
    ): DashboardOverviewResource {
        try {
            $result = $action->execute();

            return new DashboardOverviewResource($result);
        } catch (Throwable $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getEntriesVsExits(
        EntriesVsExitsRequest $request,
        GetEntriesVsExitsAction $action
    ): EntriesVsExitsResource {
        try {
            $result = $action->execute($request->getMonths());

            return new EntriesVsExitsResource($result);
        } catch (Throwable $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getMemberEngagement(
        MemberEngagementRequest $request,
        GetMemberEngagementAction $action
    ): MemberEngagementResource {
        try {
            $result = $action->execute($request->getMonth());

            return new MemberEngagementResource($result);
        } catch (Throwable $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
