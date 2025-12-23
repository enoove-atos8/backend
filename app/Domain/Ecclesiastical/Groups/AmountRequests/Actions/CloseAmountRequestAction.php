<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class CloseAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    private EntryRepositoryInterface $entryRepository;

    public function __construct(
        AmountRequestRepositoryInterface $repository,
        EntryRepositoryInterface $entryRepository
    ) {
        $this->repository = $repository;
        $this->entryRepository = $entryRepository;
    }

    /**
     * Close an amount request (creates entry for devolution if needed)
     *
     * @param  array  $entryData  Data for creating the entry record (if devolution is needed)
     *
     * @throws GeneralExceptions
     */
    public function execute(int $id, int $closedBy, int $reviewerId, array $entryData = []): bool
    {
        // Check if exists
        $existing = $this->repository->getById($id);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Only proven, partially_proven or overdue requests can be closed
        $validStatuses = [ReturnMessages::STATUS_PROVEN, ReturnMessages::STATUS_PARTIALLY_PROVEN, ReturnMessages::STATUS_OVERDUE];
        if (! in_array($existing->status, $validStatuses)) {
            throw new GeneralExceptions(ReturnMessages::INVALID_STATUS_FOR_CLOSE, 400);
        }

        $requestedAmount = (float) $existing->requestedAmount;
        $provenAmount = (float) $existing->provenAmount;
        $devolutionAmount = $requestedAmount - $provenAmount;

        $devolutionEntryId = null;

        // If there's value to be returned, create an entry
        if ($devolutionAmount > 0) {
            $entry = $this->entryRepository->create([
                'reviewer_id' => $reviewerId,
                'entry_type' => 'devolution',
                'transaction_type' => $entryData['transaction_type'] ?? 'cash',
                'transaction_compensation' => $entryData['transaction_compensation'] ?? 'immediate',
                'date_transaction_compensation' => $entryData['date_transaction_compensation'] ?? date('Y-m-d'),
                'date_entry_register' => date('Y-m-d'),
                'amount' => $devolutionAmount,
                'devolution' => true,
                'comments' => 'Devolução de verba - Solicitação #'.$id,
                'group_received_id' => $existing->groupId,
            ]);

            if ($entry === null || $entry->id === null) {
                throw new GeneralExceptions(ReturnMessages::ERROR_CLOSE_AMOUNT_REQUEST, 500);
            }

            $devolutionEntryId = $entry->id;
        }

        // Close the request
        $closed = $this->repository->close(
            $id,
            $closedBy,
            $devolutionEntryId,
            number_format($devolutionAmount, 2, '.', '')
        );

        if (! $closed) {
            throw new GeneralExceptions(ReturnMessages::ERROR_CLOSE_AMOUNT_REQUEST, 500);
        }

        return true;
    }
}
