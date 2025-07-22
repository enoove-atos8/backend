<?php

namespace Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Infrastructure\Services\ExportData\Secretary\Membership\BirthdaysExportData;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Throwable;

class ExportBirthdaysAction
{
    private MemberRepositoryInterface $memberRepository;
    private GetMembersByBornMonthAction $getMembersByBornMonthAction;
    private MinioStorageService $minioStorageService;
    private UploadDataExportedReportAction $uploadDataExportedReportAction;

    public function __construct(
        MemberRepositoryInterface $memberRepositoryInterface,
        GetMembersByBornMonthAction $getMembersByBornMonthAction,
        MinioStorageService $minioStorageService,
        UploadDataExportedReportAction $uploadDataExportedReportAction
    )
    {
        $this->memberRepository = $memberRepositoryInterface;
        $this->getMembersByBornMonthAction = $getMembersByBornMonthAction;
        $this->minioStorageService = $minioStorageService;
        $this->uploadDataExportedReportAction = $uploadDataExportedReportAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(string $month, string $type, string $fields): mixed
    {
        $members = $this->getMembersByBornMonthAction->execute($month, $fields);

        if($members->count() > 0)
        {
            $exportData = new BirthdaysExportData(
                $month,
                $type,
                $fields
            );

            $fileContent = $exportData->export($members->toArray());

            $extension = strtolower($type);
            if (str_contains($type, '/')) {
                $parts = explode('/', $type);
                $extension = end($parts);
            }
            $result = $this->uploadDataExportedReportAction->execute($month, $extension, $fileContent);

            return [
                'success' => true,
                'fileUrl' => $result['fileUrl'],
                'fileName' => $result['fileName']
            ];
        }

        return collect([]);
    }
}
