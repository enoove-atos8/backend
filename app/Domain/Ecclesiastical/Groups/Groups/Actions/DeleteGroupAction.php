<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Constants\ReturnMessages;
use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;

class DeleteGroupAction
{
    private GroupsRepository $groupsRepository;

    public function __construct(
        GroupRepositoryInterface $groupsRepositoryInterface,
    ) {
        $this->groupsRepository = $groupsRepositoryInterface;
    }

    /**
     * @return array{success: bool, message: string, balance?: float}
     *
     * @throws GeneralExceptions
     */
    public function execute(int $groupId): array
    {
        $group = $this->groupsRepository->getGroupsById($groupId);

        if (! $group) {
            throw new GeneralExceptions(ReturnMessages::GROUP_NOT_FOUND, 404);
        }

        $lastMovement = $this->groupsRepository->getGroupBalance($groupId);

        if ($lastMovement !== null && $lastMovement->balance > 0) {
            return [
                'success' => false,
                'message' => ReturnMessages::ERROR_DELETE_GROUP_HAS_BALANCE,
                'balance' => $lastMovement->balance,
            ];
        }

        $deleted = $this->groupsRepository->softDelete($groupId);

        if (! $deleted) {
            throw new GeneralExceptions(ReturnMessages::ERROR_DELETE_GROUP, 500);
        }

        return [
            'success' => true,
            'message' => ReturnMessages::GROUP_DELETED,
        ];
    }
}
