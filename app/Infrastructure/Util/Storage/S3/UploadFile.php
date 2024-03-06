<?php

namespace Infrastructure\Util\Storage\S3;

use Exception;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\PdfToImage\Pdf;

class UploadFile
{
    const ERROR_UPLOAD_AVATAR = 'Ocorreu um erro ao realizar o upload deste avatar, tente mais tarde!';

    public function __construct()
    {
    }


    /**
     * @param mixed $file
     * @param string $tenantS3PathObject
     * @param string $tenant
     * @return string
     * @throws GeneralExceptions
     */
    public function upload(mixed $file, string $tenantS3PathObject, string $tenant): string
    {
        try
        {
            $fileExtension = explode('.', $file->getClientOriginalName())[1];
            $s3 = Storage::disk('s3');
            $fileName = uniqid().'.'.$fileExtension;
            $s3Path = 'clients/' . $tenant . '/' . $tenantS3PathObject . '/' . $fileName;

            $s3->put($s3Path, file_get_contents($file));

            return $s3->url($s3Path);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions(self::ERROR_UPLOAD_AVATAR, 500);
        }
    }
}
