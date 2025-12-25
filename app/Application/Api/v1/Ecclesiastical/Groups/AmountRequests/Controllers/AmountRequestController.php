<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Controllers;

use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests\AmountRequestReceiptRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests\AmountRequestRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests\CreateAmountRequestReminderRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests\LinkExitRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests\RejectAmountRequestRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests\UpdateAmountRequestReceiptRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources\AmountRequestHistoryResourceCollection;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources\AmountRequestIndicatorsResource;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources\AmountRequestReceiptResourceCollection;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources\AmountRequestReminderResourceCollection;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources\AmountRequestResource;
use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources\AmountRequestResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\ApproveAmountRequestAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\CloseAmountRequestAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\CreateAmountRequestAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\CreateAmountRequestReceiptAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\CreateAmountRequestReminderAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\DeleteAmountRequestAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\DeleteAmountRequestReceiptAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\GetAmountRequestByIdAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\GetAmountRequestHistoryAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\GetAmountRequestIndicatorsAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\GetAmountRequestReceiptsAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\GetAmountRequestRemindersAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\GetAmountRequestsAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\LinkExitToAmountRequestAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\RejectAmountRequestAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\UpdateAmountRequestAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\UpdateAmountRequestReceiptAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class AmountRequestController extends Controller
{
    /**
     * List all amount requests
     *
     * @throws GeneralExceptions|Throwable
     */
    public function getAmountRequests(
        Request $request,
        GetAmountRequestsAction $action
    ): AmountRequestResourceCollection {
        try {
            $filters = [
                'status' => $request->input('status'),
                'group_id' => $request->input('groupId'),
                'member_id' => $request->input('memberId'),
                'date_from' => $request->input('dateFrom'),
                'date_to' => $request->input('dateTo'),
            ];

            // Remove null values
            $filters = array_filter($filters, fn ($value) => $value !== null);

            $response = $action->execute($filters);

            return new AmountRequestResourceCollection($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Get a single amount request by ID
     *
     * @throws GeneralExceptions|Throwable
     */
    public function getAmountRequestById(
        int $id,
        GetAmountRequestByIdAction $action
    ): AmountRequestResource {
        try {
            $response = $action->execute($id);

            return new AmountRequestResource($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Create a new amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function createAmountRequest(
        AmountRequestRequest $request,
        CreateAmountRequestAction $action
    ): Response {
        try {
            $action->execute($request->amountRequestData());

            return response([
                'message' => ReturnMessages::AMOUNT_REQUEST_CREATED,
            ], 201);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Update an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function updateAmountRequest(
        int $id,
        AmountRequestRequest $request,
        UpdateAmountRequestAction $action
    ): Response {
        try {
            $action->execute($id, $request->amountRequestData());

            return response([
                'message' => ReturnMessages::AMOUNT_REQUEST_UPDATED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Delete an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function deleteAmountRequest(
        int $id,
        DeleteAmountRequestAction $action
    ): Response {
        try {
            $action->execute($id);

            return response([
                'message' => ReturnMessages::AMOUNT_REQUEST_DELETED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Approve an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function approveAmountRequest(
        int $id,
        Request $request,
        ApproveAmountRequestAction $action
    ): Response {
        try {
            $action->execute($id, $request->user()->id);

            return response([
                'message' => ReturnMessages::AMOUNT_REQUEST_APPROVED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Reject an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function rejectAmountRequest(
        int $id,
        RejectAmountRequestRequest $request,
        RejectAmountRequestAction $action
    ): Response {
        try {
            $action->execute(
                $id,
                $request->user()->id,
                $request->input('rejectionReason')
            );

            return response([
                'message' => ReturnMessages::AMOUNT_REQUEST_REJECTED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Link or unlink an exit to an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function linkExitToAmountRequest(
        int $id,
        LinkExitRequest $request,
        LinkExitToAmountRequestAction $action
    ): Response {
        try {
            $exitId = $request->input('transferExitId');
            $action->execute($id, $exitId, $request->user()->id);

            $message = $exitId !== null
                ? ReturnMessages::EXIT_LINKED
                : ReturnMessages::EXIT_UNLINKED;

            return response([
                'message' => $message,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Close an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function closeAmountRequest(
        int $id,
        Request $request,
        CloseAmountRequestAction $action
    ): Response {
        try {
            $action->execute($id, $request->user()->id);

            return response([
                'message' => ReturnMessages::AMOUNT_REQUEST_CLOSED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * List all receipts for an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function getAmountRequestReceipts(
        int $id,
        GetAmountRequestReceiptsAction $action
    ): AmountRequestReceiptResourceCollection {
        try {
            $response = $action->execute($id);

            return new AmountRequestReceiptResourceCollection($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Add a receipt to an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function createAmountRequestReceipt(
        int $id,
        AmountRequestReceiptRequest $request,
        CreateAmountRequestReceiptAction $action
    ): Response {
        try {
            $file = $request->file('file');
            $path = $request->input('path');
            $action->execute($request->receiptData($id), $file, $path);

            return response([
                'message' => ReturnMessages::RECEIPT_CREATED,
            ], 201);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Delete a receipt from an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function deleteAmountRequestReceipt(
        int $id,
        int $receiptId,
        Request $request,
        DeleteAmountRequestReceiptAction $action
    ): Response {
        try {
            $action->execute($id, $receiptId, $request->user()->id);

            return response([
                'message' => ReturnMessages::RECEIPT_DELETED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Update a receipt from an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function updateAmountRequestReceipt(
        int $id,
        int $receiptId,
        UpdateAmountRequestReceiptRequest $request,
        UpdateAmountRequestReceiptAction $action
    ): Response {
        try {
            $action->execute($id, $receiptId, $request->receiptData($id), $request->user()->id);

            return response([
                'message' => ReturnMessages::RECEIPT_UPDATED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Get indicators/summary for dashboard
     *
     * @throws GeneralExceptions|Throwable
     */
    public function getAmountRequestIndicators(
        Request $request,
        GetAmountRequestIndicatorsAction $action
    ): AmountRequestIndicatorsResource {
        try {
            $groupId = $request->input('groupId');
            $response = $action->execute($groupId ? (int) $groupId : null);

            return new AmountRequestIndicatorsResource($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * List all reminders for an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function getAmountRequestReminders(
        int $id,
        GetAmountRequestRemindersAction $action
    ): AmountRequestReminderResourceCollection {
        try {
            $response = $action->execute($id);

            return new AmountRequestReminderResourceCollection($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Create a reminder for an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function createAmountRequestReminder(
        int $id,
        CreateAmountRequestReminderRequest $request,
        CreateAmountRequestReminderAction $action
    ): Response {
        try {
            $action->execute($request->reminderData($id));

            return response([
                'message' => ReturnMessages::REMINDER_CREATED,
            ], 201);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Get history/timeline for an amount request
     *
     * @throws GeneralExceptions|Throwable
     */
    public function getAmountRequestHistory(
        int $id,
        GetAmountRequestHistoryAction $action
    ): AmountRequestHistoryResourceCollection {
        try {
            $response = $action->execute($id);

            return new AmountRequestHistoryResourceCollection($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
