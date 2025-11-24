<?php

namespace App\Domain\CentralDomain\Churches\Church\Actions;

use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;

class UpdateChurchAction
{
    public function __construct(
        private ChurchRepositoryInterface $churchRepository
    ) {}

    /**
     * Update church data
     *
     * @param  array  $data  Data to update
     */
    public function execute(int $churchId, array $data): bool
    {
        return $this->churchRepository->updateChurch($churchId, $data);
    }
}
