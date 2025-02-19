<?php

namespace Application\Api\v1\Mobile\SyncStorage\Controllers;

use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;

class UploadReceiptController extends Controller
{
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
            $path = $request->input('path');
            $file = $request->files->get('file');
            $tenant = explode('.', $request->getHost())[0];

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
