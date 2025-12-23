<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Soft delete an amount request
     *
     * @throws GeneralExceptions
     */
    public function execute(int $id): bool
    {
        // Check if exists
        $existing = $this->repository->getById($id);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Only pending requests can be deleted
        if ($existing->status !== ReturnMessages::STATUS_PENDING) {
            throw new GeneralExceptions('Apenas solicitações pendentes podem ser excluídas!', 400);
        }

        $deleted = $this->repository->delete($id);

        if (! $deleted) {
            throw new GeneralExceptions(ReturnMessages::ERROR_DELETE_AMOUNT_REQUEST, 500);
        }

        return true;
    }
}
