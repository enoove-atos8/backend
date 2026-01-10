<?php

namespace Application\Core\Listeners\Ecclesiastical\Groups\AmountRequests;

use App\Application\Core\Jobs\WhatsApp\Ecclesiastical\Groups\AmountRequests\ProcessWhatsAppReminderJob;
use Application\Core\Events\Ecclesiastical\Groups\AmountRequests\AmountRequestStatusChanged;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendAmountRequestWhatsAppNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private AmountRequestRepositoryInterface $amountRequestRepository
    ) {}

    /**
     * Handle the event.
     */
    public function handle(AmountRequestStatusChanged $event): void
    {
        // Mapeamento: Status da Solicitação → Tipo de Notificação
        // Apenas envia WhatsApp na aprovação, transferência, comprovação e encerramento
        $statusToReminderType = [
            ReturnMessages::STATUS_APPROVED => 'request_approved',
            ReturnMessages::STATUS_TRANSFERRED => 'transfer_completed',
            ReturnMessages::STATUS_PARTIALLY_PROVEN => 'proof_in_progress',
            ReturnMessages::STATUS_PROVEN => 'proof_completed',
            ReturnMessages::STATUS_OVERDUE => 'proof_overdue',
            ReturnMessages::STATUS_CLOSED => 'request_closed',
        ];

        // Verifica se deve criar reminder para este status
        if (! isset($statusToReminderType[$event->newStatus])) {
            Log::info("Status {$event->newStatus} não requer notificação WhatsApp");

            return;
        }

        $reminderType = $statusToReminderType[$event->newStatus];

        try {
            // Cria o reminder no banco
            $reminderId = $this->amountRequestRepository->createReminder(
                new AmountRequestReminderData(
                    amountRequestId: $event->amountRequestId,
                    type: $reminderType,
                    channel: 'whatsapp',
                    scheduledAt: now()->toDateTimeString(),
                    status: 'pending',
                    metadata: $event->additionalData
                )
            );

            // Dispara o Job para processar e enviar a mensagem
            ProcessWhatsAppReminderJob::dispatch($reminderId)
                ->onQueue('whatsapp');

            Log::info('Reminder criado e job disparado', [
                'reminder_id' => $reminderId,
                'amount_request_id' => $event->amountRequestId,
                'type' => $reminderType,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar reminder WhatsApp', [
                'amount_request_id' => $event->amountRequestId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
