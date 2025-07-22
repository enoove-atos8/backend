<?php

namespace Domain\Secretary\Membership\Actions;

use Infrastructure\Services\External\minIO\MinioStorageService;
use Throwable;

class UploadDataExportedReportAction
{
    private MinioStorageService $minioStorageService;
    private const S3_PATH = 'reports/secretary/membership/birthdays';

    public function __construct(MinioStorageService $minioStorageService)
    {
        $this->minioStorageService = $minioStorageService;
    }

    /**
     * @throws Throwable
     */
    public function execute(string $month, string $type, string $fileContent): array
    {
        $fileName = 'birthdays_' . $month . '_' . time() . '.' . $type;
        $s3Path = self::S3_PATH;

        // Salvar temporariamente o arquivo para upload
        $tempPath = storage_path('app/temp');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }

        $filePath = $tempPath . '/' . $fileName;
        file_put_contents($filePath, $fileContent);

        // Upload para o MinIO usando o serviço injetado
        $tenant = explode('.', request()->getHost())[0];
        $fileUrl = $this->minioStorageService->upload($filePath, $s3Path, $tenant);

        // Remover arquivo temporário após o upload
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return [
            'fileUrl' => $fileUrl,
            'fileName' => $fileName
        ];
    }
}
