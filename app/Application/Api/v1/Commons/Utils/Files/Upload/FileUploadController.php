<?php

namespace Application\Api\v1\Commons\Utils\Files\Upload;

use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use Application\Api\v1\Financial\Entries\Entries\Requests\ReceiptEntryRequest;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;

class FileUploadController extends Controller
{
    const MODULES_FEATURES_S3_PATHS = [
        'entries'    =>  [
            'receipts'  =>  'entries/assets/receipts'
        ],
        'exits'    =>  [],
        'users'    =>  [
            'avatars'   =>  ''
        ],
        'members'    =>  [
            'avatars'   =>  ''
        ],
    ];


    /**
     * @param Request $request
     * @param UploadFile $uploadFile
     * @return Response
     * @throws GeneralExceptions
     */
    public function fileUpload(Request $request, UploadFile $uploadFile): Response
    {
        try
        {
            $module = $request->input('module');
            $feature = $request->input('feature');

            $path = self::MODULES_FEATURES_S3_PATHS[$module][$feature];
            $tenant = explode('.', $request->getHost())[0];
            $file = $request->files->get('file');

            $response = $uploadFile->upload($file, $path, $tenant);

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
