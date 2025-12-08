<?php

namespace Application\Api\v1\Secretary\Membership\Membership\Controllers;

use App\Domain\Secretary\Membership\Actions\ExportTithersAction;
use App\Domain\Secretary\Membership\Actions\GetMembersByGroupIdAction;
use App\Domain\SyncStorage\Constants\ReturnMessages;
use Application\Api\v1\Secretary\Membership\Membership\Requests\BatchMemberRequest;
use Application\Api\v1\Secretary\Membership\Membership\Requests\MemberAvatarRequest;
use Application\Api\v1\Secretary\Membership\Membership\Requests\MemberRequest;
use Domain\Secretary\Membership\Actions\BatchCreateMembersAction;
use Application\Api\v1\Secretary\Membership\Membership\Resources\MemberResource;
use Application\Api\v1\Secretary\Membership\Membership\Resources\MemberResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Secretary\Membership\Actions\CreateMemberAction;
use Domain\Secretary\Membership\Actions\ExportBirthdaysAction;
use Domain\Secretary\Membership\Actions\GetMemberByIdAction;
use Domain\Secretary\Membership\Actions\GetMembersAction;
use Domain\Secretary\Membership\Actions\GetMembersByBornMonthAction;
use Domain\Secretary\Membership\Actions\GetMembersCountersAction;
use Domain\Secretary\Membership\Actions\GetTithersByMonthAction;
use Domain\Secretary\Membership\Actions\UpdateMemberAction;
use Domain\Secretary\Membership\Actions\UpdateStatusMemberAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class MemberController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['role:admin|secretary']);
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function createMember(MemberRequest $memberRequest, CreateMemberAction $createMemberAction): Response
    {
        try {
            $createMemberAction->execute($memberRequest->memberData());

            return response([
                'message' => ReturnMessages::SUCCESS_MEMBER_REGISTERED,
            ], 201);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getMembers(Request $request, GetMembersAction $getMembersAction): MemberResourceCollection
    {
        try {
            $filters = $request->except(['page', 'term']);
            $term = $request->input('term');
            $paginate = $request->input('page') == true;
            $response = $getMembersAction->execute($filters, $term, $paginate)['results'];
            $countRows = $getMembersAction->execute($filters, $term, $paginate)['countRows'];

            return new MemberResourceCollection($response, $countRows);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function exportBirthdaysData(Request $request, ExportBirthdaysAction $exportBirthdaysAction): Response
    {
        try {
            $month = $request->input('month');
            $type = $request->input('type');
            $fields = $request->input('fields');

            $result = $exportBirthdaysAction->execute($month, $type, $fields);

            return response([
                'success' => true,
                'message' => 'Relatório gerado com sucesso',
                'fileUrl' => $result['fileUrl'] ?? null,
                'fileName' => $result['fileName'] ?? null,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function exportTithersData(Request $request, ExportTithersAction $exportTithersAction): Response
    {
        try {
            $month = $request->input('month');
            $type = $request->input('type');

            $result = $exportTithersAction->execute($month, $type);

            return response([
                'success' => true,
                'message' => 'Relatório gerado com sucesso',
                'fileUrl' => $result['fileUrl'] ?? null,
                'fileName' => $result['fileName'] ?? null,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     */
    public function getMembersIndicators(GetMembersCountersAction $getMembersCountersAction): Response
    {
        try {
            $response = $getMembersCountersAction->execute();

            if ($response) {
                return response(
                    $response, 200);
            }

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function getMemberById($id, GetMemberByIdAction $getMemberByIdAction): MemberResource
    {
        try {
            $response = $getMemberByIdAction->execute($id);

            return new MemberResource($response);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function getMembersByBornMonth(Request $request, GetMembersByBornMonthAction $getMembersByBornMonthAction): MemberResourceCollection
    {
        try {
            $date = $request->input('date');
            $response = $getMembersByBornMonthAction->execute($date);

            return new MemberResourceCollection($response);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function getTithersByDate(Request $request, GetTithersByMonthAction $getTithersByMonthAction): MemberResourceCollection
    {
        try {
            $month = $request->input('month');
            $response = $getTithersByMonthAction->execute($month, true);

            return new MemberResourceCollection($response);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     */
    public function updateStatus(Request $request, $id, UpdateStatusMemberAction $updateStatusMemberAction): Response
    {
        try {
            $activated = $request->input('activated');
            $response = $updateStatusMemberAction->execute($id, $activated);
            if ($response) {
                return response([
                    'message' => ReturnMessages::SUCCESS_UPDATE_STATUS_MEMBER,
                ], 200);
            }
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function updateMember(MemberRequest $memberRequest, $id, UpdateMemberAction $updateMemberAction): Response
    {
        try {
            $response = $updateMemberAction->execute($id, $memberRequest->memberData());

            if ($response) {
                return response([
                    'message' => ReturnMessages::SUCCESS_UPDATED_MEMBER,
                ], 200);
            }
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     */
    public function uploadMemberAvatar(MemberAvatarRequest $memberAvatarRequest, UploadFile $uploadFile): Response
    {
        try {
            $tenantS3PathObject = 'members/assets/avatars';
            $tenant = explode('.', $memberAvatarRequest->getHost())[0];
            $response = $uploadFile->upload($memberAvatarRequest->files->get('avatar'), $tenantS3PathObject, $tenant);

            if ($response) {
                return response([
                    'message' => ReturnMessages::SUCCESS_UPDATE_IMAGE_MEMBER,
                    'avatar' => $response,
                ], 200);
            }
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function getMembersByGroupId(Request $request, GetMembersByGroupIdAction $getMembersByGroupIdAction): MemberResourceCollection
    {
        try {
            $groupId = $request->input('groupId');
            $response = $getMembersByGroupIdAction->execute((int) $groupId);

            return new MemberResourceCollection($response);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function batchCreateMembers(BatchMemberRequest $batchMemberRequest, BatchCreateMembersAction $batchCreateMembersAction): Response
    {
        try {
            $membersData = $batchMemberRequest->membersData();
            $result = $batchCreateMembersAction->execute($membersData);

            if ($result) {
                return response([
                    'success' => true,
                    'data' => [
                        'total' => count($membersData),
                        'imported' => count($membersData),
                        'failed' => 0,
                        'errors' => [],
                    ],
                ], 201);
            }

            return response([
                'success' => false,
                'message' => 'Erro ao importar membros',
            ], 500);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
