<?php

namespace Application\Api\v1\Commons\Utils\Files\Upload;

use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use Application\Api\v1\Financial\Entries\Entries\Requests\ReceiptEntryRequest;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Infrastructure\Util\Storage\S3\UploadFile;

class FileUploadController extends Controller
{
    private MinioStorageService $minioStorageService;

    public function __construct(MinioStorageService $minioStorageService)
    {
        $this->minioStorageService = $minioStorageService;
    }

    const S3_BASE_PATHS_STORED_RECEIPTS = [
        'financial'    =>  [
            'shared'  =>  'sync_storage/financial/shared_receipts',
            'stored'  =>  'sync_storage/financial/stored_receipts',
        ],
        'users'    =>  [
            'avatars'   =>  'users/assets/avatars'
        ],
        'members'    =>  [
            'avatars'   =>  'members/assets/avatars'
        ],
    ];


    /**
     * Generic file upload function
     * Uploads a file to MinIO with custom path
     *
     * @param Request $request
     * @return Response
     * @throws GeneralExceptions
     */
    public function uploadFile(Request $request): Response
    {
        try
        {
            $tenant = explode('.', $request->getHost())[0];
            $relativePath = $request->input('relativePath');
            $file = $request->files->get('file');

            $response = $this->minioStorageService->upload($file, $relativePath, $tenant);

            if (!empty($response)) {
                return response(['link' => $response], 200);
            } else {
                return response(['link' => 'error'], 500);
            }
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws GeneralExceptions
     */
    public function fileUpload(Request $request): Response
    {
        try
        {
            $module = $request->input('module');
            $typeDir = $request->input('typeDir');
            $relativePath = $request->input('relativePath');

            $path = self::S3_BASE_PATHS_STORED_RECEIPTS[$module][$typeDir] . '/' . $relativePath;
            $tenant = explode('.', $request->getHost())[0];
            $file = $request->files->get('file');

            $response = $this->minioStorageService->upload($file, $path, $tenant);

            if(!empty($response))
                return response(['link'   =>  $response], 200);
            else
                return response(['link'   =>  'error'], 500);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
