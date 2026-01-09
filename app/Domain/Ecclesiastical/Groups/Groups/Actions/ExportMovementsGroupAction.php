<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Domain\Financial\Movements\Actions\GetMovementsByGroupAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Services\ExportData\Financial\Movements\MovementsGroupExportData;
use Infrastructure\Services\External\minIO\MinioStorageService;
use Throwable;
use function Domain\Ecclesiastical\Groups\Actions\collect;
use function Domain\Ecclesiastical\Groups\Actions\end;
use function Domain\Ecclesiastical\Groups\Actions\explode;
use function Domain\Ecclesiastical\Groups\Actions\file_exists;
use function Domain\Ecclesiastical\Groups\Actions\file_put_contents;
use function Domain\Ecclesiastical\Groups\Actions\mkdir;
use function Domain\Ecclesiastical\Groups\Actions\storage_path;
use function Domain\Ecclesiastical\Groups\Actions\str_contains;
use function Domain\Ecclesiastical\Groups\Actions\strtolower;
use function Domain\Ecclesiastical\Groups\Actions\time;
use function Domain\Ecclesiastical\Groups\Actions\unlink;

class ExportMovementsGroupAction
{
    private const S3_PATH = 'reports/ecclesiastical/groups/movements';

    public function __construct(
        private GetMovementsByGroupAction $getMovementsByGroupAction,
        private GroupRepositoryInterface $groupRepository,
        private MinioStorageService $minioStorageService,
        private GetChurchAction $getChurchAction
    ) {
    }

    /**
     * @param int $groupId
     * @param string $dates
     * @param string $type
     * @param bool $paginate
     * @return mixed
     * @throws Throwable
     * @throws GeneralExceptions
     */
    public function execute(int $groupId, string $dates, string $type, bool $paginate): mixed
    {
        $movements = $this->getMovementsByGroupAction->execute($groupId, $dates, $paginate);

        if ($movements->count() > 0) {
            $group = $this->groupRepository->getGroupsById($groupId);
            $groupName = $group?->name ?? 'Grupo não encontrado';

            // Buscar dados da igreja
            $tenant = explode('.', request()->getHost())[0];
            $church = $this->getChurchAction->execute($tenant);

            $exportData = new MovementsGroupExportData(
                $groupId,
                $dates,
                $type
            );

            // Converter cada DTO para array mantendo a estrutura
            $movementsArray = $movements->map(function ($movement) {
                return $movement->toArray();
            })->toArray();

            $fileContent = $exportData->export($movementsArray, $groupName, $church);

            $extension = strtolower($type);
            if (str_contains($type, '/')) {
                $parts = explode('/', $type);
                $extension = end($parts);
            }

            $result = $this->uploadReport($groupId, $dates, $extension, $fileContent);

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
    private function uploadReport(int $groupId, string $dates, string $extension, string $fileContent): array
    {
        $fileName = 'movements_group_' . $groupId . '_' . $dates . '_' . time() . '.' . $extension;
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
