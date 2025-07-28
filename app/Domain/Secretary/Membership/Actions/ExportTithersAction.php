<?php

namespace App\Domain\Secretary\Membership\Actions;

use App\Domain\Financial\Settings\Actions\GetFinancialSettingsAction;
use App\Infrastructure\Services\ExportData\Secretary\Membership\TithersExportData;
use Domain\Secretary\Membership\Actions\GetTithersByMonthAction;
use Domain\Secretary\Membership\Actions\UploadDataExportedReportAction;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Throwable;

class ExportTithersAction
{
    private MemberRepositoryInterface $memberRepository;
    private GetTithersByMonthAction $getTithersByMonthAction;
    private GetFinancialSettingsAction $getFinancialSettingsAction;
    private MinioStorageService $minioStorageService;
    private UploadDataExportedReportAction $uploadDataExportedReportAction;

    public function __construct(
        MemberRepositoryInterface $memberRepositoryInterface,
        GetTithersByMonthAction $getTithersByMonthAction,
        MinioStorageService $minioStorageService,
        UploadDataExportedReportAction $uploadDataExportedReportAction,
        GetFinancialSettingsAction $getFinancialSettingsAction
    )
    {
        $this->memberRepository = $memberRepositoryInterface;
        $this->getTithersByMonthAction = $getTithersByMonthAction;
        $this->minioStorageService = $minioStorageService;
        $this->uploadDataExportedReportAction = $uploadDataExportedReportAction;
        $this->getFinancialSettingsAction = $getFinancialSettingsAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(string $month, string $type): mixed
    {
        $members = $this->getTithersByMonthAction->execute($month);
        $monthlyTarget = $this->getFinancialSettingsAction->execute();

        if($members->count() > 0)
        {
            $exportData = new TithersExportData(
                $month,
                $type,
                $monthlyTarget->monthly_budget_tithes
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
