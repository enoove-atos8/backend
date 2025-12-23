<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Update an amount request
     *
     * @throws GeneralExceptions
     */
    public function execute(int $id, AmountRequestData $data): bool
    {
        // Check if exists
        $existing = $this->repository->getById($id);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Only pending requests can be updated
        if ($existing->status !== ReturnMessages::STATUS_PENDING) {
            throw new GeneralExceptions('Apenas solicitações pendentes podem ser editadas!', 400);
        }

        $updated = $this->repository->update($id, $data);

        if (! $updated) {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_AMOUNT_REQUEST, 500);
        }

        return true;
    }
}
