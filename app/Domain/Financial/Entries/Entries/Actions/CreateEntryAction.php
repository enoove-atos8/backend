<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Consolidation\Actions\CreateConsolidatedEntryAction;
use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Models\Entry;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages as AmountRequestReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Financial\Movements\Actions\CreateMovementAction;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class CreateEntryAction
{
    private EntryRepositoryInterface $entryRepository;

    private ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepository;

    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;

    private CreateMovementAction $createMovementAction;

    private MovementsData $movementsData;

    private MovementRepositoryInterface $movementRepository;

    private GroupRepositoryInterface $groupRepository;

    private AmountRequestRepositoryInterface $amountRequestRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepositoryInterface,
        CreateConsolidatedEntryAction $createConsolidatedEntryAction,
        CreateMovementAction $createMovementAction,
        MovementsData $movementsData,
        MovementRepositoryInterface $movementRepositoryInterface,
        GroupRepositoryInterface $groupRepositoryInterface,
        AmountRequestRepositoryInterface $amountRequestRepositoryInterface
    ) {
        $this->entryRepository = $entryRepositoryInterface;
        $this->consolidatedEntriesRepository = $consolidatedEntriesRepositoryInterface;
        $this->createConsolidatedEntryAction = $createConsolidatedEntryAction;
        $this->createMovementAction = $createMovementAction;
        $this->movementsData = $movementsData;
        $this->movementRepository = $movementRepositoryInterface;
        $this->groupRepository = $groupRepositoryInterface;
        $this->amountRequestRepository = $amountRequestRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(EntryData $entryData, ?ConsolidationEntriesData $consolidationEntriesData): ?Entry
    {
        $dateEntryRegister = $entryData->dateEntryRegister;
        $dateTransactionCompensation = $entryData->dateTransactionCompensation;

        if ($dateTransactionCompensation !== null) {
            if (substr($dateEntryRegister, 0, 7) !== substr($dateTransactionCompensation, 0, 7)) {
                $entryData->dateEntryRegister = substr($dateTransactionCompensation, 0, 7).'-01';
            }
        }

        if (! is_null($consolidationEntriesData)) {
            $this->createConsolidatedEntryAction->execute($consolidationEntriesData);
        }

        $entry = $this->entryRepository->newEntry($entryData);
        $entryData->id = $entry->id;

        if (! is_null($entryData->id)) {
            if ($entryData->entryType == EntryRepository::DESIGNATED_VALUE) {
                $group = $this->groupRepository->getGroupById($entryData->groupReceivedId);

                if ($group && $group->financialMovement) {
                    $movementData = $this->movementsData::fromObjectData($entryData);
                    $this->createMovementAction->execute($movementData);
                }
            }

            // Auto-link devolution entry to open amount request
            $this->linkDevolutionToAmountRequest($entryData);

            return $entry;
        } else {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_ENTRY, 500);
        }
    }

    /**
     * Link devolution entry to open amount request if applicable
     */
    private function linkDevolutionToAmountRequest(EntryData $entryData): void
    {
        // Only process if this is a devolution entry
        if ($entryData->devolution !== 1) {
            return;
        }

        // groupReturnedId is the group that is returning money
        if ($entryData->groupReturnedId === null) {
            return;
        }

        // Find open amount request for this group
        $openRequest = $this->amountRequestRepository->getOpenByGroupId($entryData->groupReturnedId);

        if ($openRequest === null) {
            return;
        }

        // Link the devolution entry to the amount request
        $linked = $this->amountRequestRepository->linkDevolution(
            $openRequest->id,
            $entryData->id,
            $entryData->amount
        );

        // Register history event if link was successful
        if ($linked) {
            $devolutionAmount = (float) $entryData->amount;

            $this->amountRequestRepository->createHistory(new AmountRequestHistoryData(
                amountRequestId: $openRequest->id,
                event: AmountRequestReturnMessages::HISTORY_EVENT_DEVOLUTION_LINKED,
                description: AmountRequestReturnMessages::HISTORY_DESCRIPTIONS[AmountRequestReturnMessages::HISTORY_EVENT_DEVOLUTION_LINKED],
                userId: $entryData->reviewerId,
                metadata: [
                    'entry_id' => $entryData->id,
                    'devolution_amount' => $devolutionAmount,
                ]
            ));

            // Auto-close if provenAmount + devolutionAmount >= requestedAmount
            $this->autoCloseIfComplete($openRequest->id, $entryData->reviewerId);
        }
    }

    /**
     * Auto-close amount request if proven + devolution >= requested
     */
    private function autoCloseIfComplete(int $amountRequestId, int $closedBy): void
    {
        // Reload the request to get updated values
        $request = $this->amountRequestRepository->getById($amountRequestId);

        if ($request === null) {
            return;
        }

        $requestedAmount = (float) $request->requestedAmount;
        $provenAmount = (float) ($request->provenAmount ?? 0);
        $devolutionAmount = (float) ($request->devolutionAmount ?? 0);

        // Check if proven + devolution covers the requested amount
        if (($provenAmount + $devolutionAmount) >= $requestedAmount) {
            // Close the request automatically
            $closed = $this->amountRequestRepository->close($amountRequestId, $closedBy);

            if ($closed) {
                $this->amountRequestRepository->createHistory(new AmountRequestHistoryData(
                    amountRequestId: $amountRequestId,
                    event: AmountRequestReturnMessages::HISTORY_EVENT_CLOSED,
                    description: 'Solicitação fechada automaticamente (comprovado + devolvido = solicitado)',
                    userId: $closedBy,
                    metadata: [
                        'requested_amount' => $requestedAmount,
                        'proven_amount' => $provenAmount,
                        'devolution_amount' => $devolutionAmount,
                        'auto_closed' => true,
                    ]
                ));
            }
        }
    }
}
