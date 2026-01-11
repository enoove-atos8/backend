<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Actions\GetEntryByIdAction;
use Application\Core\Events\Ecclesiastical\Groups\AmountRequests\AmountRequestStatusChanged;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class CloseAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    private GetEntryByIdAction $getEntryByIdAction;

    private GroupRepositoryInterface $groupRepository;

    public function __construct(
        AmountRequestRepositoryInterface $repository,
        GetEntryByIdAction $getEntryByIdAction,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->repository = $repository;
        $this->getEntryByIdAction = $getEntryByIdAction;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Close an amount request manually
     *
     * @throws GeneralExceptions
     */
    public function execute(int $id, int $closedBy, ?int $linkedGroupDevolutionEntryId = null): bool
    {
        // Check if exists
        $existing = $this->repository->getById($id);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        $requestedAmount = (float) $existing->requestedAmount;
        $provenAmount = (float) $existing->provenAmount;
        $devolutionAmount = (float) ($existing->devolutionAmount ?? 0);

        // Se uma entrada de devolução para o grupo foi vinculada
        if ($linkedGroupDevolutionEntryId !== null) {
            // Busca a entrada
            $entry = $this->getEntryByIdAction->execute($linkedGroupDevolutionEntryId);

            // Valida que é uma entrada de devolução para o grupo
            if ($entry->group_devolution != 1) {
                throw new GeneralExceptions('A entrada vinculada não é uma devolução para o grupo!', 400);
            }

            // CONDITIONAL VALIDATION based on request type
            $requestType = $existing->type ?? ReturnMessages::TYPE_GROUP_FUND;

            if ($requestType === ReturnMessages::TYPE_GROUP_FUND) {
                // Devolution goes to the same group (original logic)
                if ($entry->group_received_id != $existing->groupId) {
                    throw new GeneralExceptions('A entrada vinculada não pertence ao mesmo grupo da solicitação!', 400);
                }
            } elseif ($requestType === ReturnMessages::TYPE_MINISTERIAL_INVESTMENT) {
                // Devolution goes to Financial Ministry
                $financialGroup = $this->groupRepository->getFinancialGroup();

                if ($financialGroup === null) {
                    throw new GeneralExceptions('Grupo Ministério de Finanças não encontrado!', 500);
                }

                if ($entry->group_received_id != $financialGroup->id) {
                    throw new GeneralExceptions('A entrada de devolução deve ser para o Ministério de Finanças!', 400);
                }
            }

            // Valida que a entrada ainda não foi vinculada a outra solicitação
            $entryAlreadyLinked = $this->repository->checkIfEntryIsLinked($linkedGroupDevolutionEntryId, $id);
            if ($entryAlreadyLinked) {
                throw new GeneralExceptions('Esta entrada já está vinculada a outra solicitação de verba!', 400);
            }

            $devolutionAmount = (float) $entry->amount;

            // Valida que proven_amount + devolution_amount >= requested_amount
            if (($provenAmount + $devolutionAmount) < $requestedAmount) {
                throw new GeneralExceptions(
                    'O valor comprovado ('.number_format($provenAmount, 2, ',', '.').
                    ') + valor da entrada ('.number_format($devolutionAmount, 2, ',', '.').
                    ') não totaliza o valor solicitado ('.number_format($requestedAmount, 2, ',', '.').')!',
                    400
                );
            }

            // Atualiza a solicitação com a entrada de devolução vinculada
            $this->repository->linkDevolution($id, $linkedGroupDevolutionEntryId, $devolutionAmount);
        }

        // Only proven requests can be closed (all amount was proven)
        // Or requests with devolution already linked (proven + devolution = requested)
        $validStatuses = [ReturnMessages::STATUS_PROVEN];

        // Allow closing partially_proven or overdue if devolution is already linked
        if ($existing->devolutionEntryId !== null || $linkedGroupDevolutionEntryId !== null) {
            $validStatuses[] = ReturnMessages::STATUS_PARTIALLY_PROVEN;
            $validStatuses[] = ReturnMessages::STATUS_OVERDUE;
        }

        if (! in_array($existing->status, $validStatuses)) {
            throw new GeneralExceptions(ReturnMessages::INVALID_STATUS_FOR_CLOSE, 400);
        }

        $oldStatus = $existing->status;

        // Close the request
        $closed = $this->repository->close($id, $closedBy);

        if (! $closed) {
            throw new GeneralExceptions(ReturnMessages::ERROR_CLOSE_AMOUNT_REQUEST, 500);
        }

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $id,
            event: ReturnMessages::HISTORY_EVENT_CLOSED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_CLOSED],
            userId: $closedBy,
            metadata: [
                ReturnMessages::METADATA_KEY_REQUESTED_AMOUNT => $requestedAmount,
                ReturnMessages::METADATA_KEY_PROVEN_AMOUNT => $provenAmount,
                ReturnMessages::METADATA_KEY_DEVOLUTION_AMOUNT => $devolutionAmount,
                ReturnMessages::METADATA_KEY_LINKED_ENTRY_ID => $linkedGroupDevolutionEntryId,
            ]
        ));

        // Dispatch Event para notificação WhatsApp
        event(new AmountRequestStatusChanged(
            amountRequestId: $id,
            oldStatus: $oldStatus,
            newStatus: ReturnMessages::STATUS_CLOSED,
            userId: $closedBy,
            additionalData: [
                'requested_amount' => $requestedAmount,
                'proven_amount' => $provenAmount,
                'devolution_amount' => $devolutionAmount,
            ]
        ));

        return true;
    }
}
