<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use App\Domain\Secretary\Membership\Actions\GetMembersByGroupIdAction;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Services\ExportData\Ecclesiastical\Groups\GroupTithersExportData;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Throwable;

class ExportGroupTithersAction
{
    private const S3_PATH = 'reports/ecclesiastical/groups/tithers';

    public function __construct(
        private GetMembersByGroupIdAction $getMembersByGroupIdAction,
        private GroupRepositoryInterface $groupRepository,
        private MinioStorageService $minioStorageService,
        private GetChurchAction $getChurchAction
    ) {
    }

    /**
     * @param int $groupId
     * @param string $type
     * @return mixed
     * @throws Throwable
     * @throws GeneralExceptions
     */
    public function execute(int $groupId, string $type): mixed
    {
        $members = $this->getMembersByGroupIdAction->execute($groupId);

        if ($members && $members->count() > 0) {
            $group = $this->groupRepository->getGroupsById($groupId);
            $groupName = $group?->name ?? 'Grupo não encontrado';

            // Buscar dados da igreja
            $tenant = explode('.', request()->getHost())[0];
            $church = $this->getChurchAction->execute($tenant);

            $exportData = new GroupTithersExportData(
                $groupId,
                $type
            );

            // Converter cada DTO para array mantendo a estrutura
            $membersArray = $members->map(function ($member) {
                return $member->toArray();
            })->toArray();

            $fileContent = $exportData->export($membersArray, $groupName, $church);

            $extension = strtolower($type);
            if (str_contains($type, '/')) {
                $parts = explode('/', $type);
                $extension = end($parts);
            }

            $result = $this->uploadReport($groupId, $extension, $fileContent);

            return [
                'success' => true,
                'fileUrl' => $result['fileUrl'],
                'fileName' => $result['fileName'],
            ];
        }

        return collect([]);
    }

    /**
     * @throws Throwable
     */
    private function uploadReport(int $groupId, string $extension, string $fileContent): array
    {
        $fileName = 'group_tithers_' . $groupId . '_' . time() . '.' . $extension;
        $s3Path = self::S3_PATH;

        $tempPath = storage_path('app/temp');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }

        $filePath = $tempPath . '/' . $fileName;
        file_put_contents($filePath, $fileContent);

        $tenant = explode('.', request()->getHost())[0];
        $fileUrl = $this->minioStorageService->upload($filePath, $s3Path, $tenant);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return [
            'fileUrl' => $fileUrl,
            'fileName' => $fileName,
        ];
    }
}
