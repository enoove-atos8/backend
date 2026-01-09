<?php

namespace Application\Core\Events\Ecclesiastical\Groups\AmountRequests;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AmountRequestStatusChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  int  $amountRequestId  ID da solicitação de verba
     * @param  string  $oldStatus  Status anterior
     * @param  string  $newStatus  Novo status
     * @param  int  $userId  ID do usuário que fez a alteração
     * @param  array  $additionalData  Dados adicionais (ex: motivo de rejeição, exit_id, etc)
     */
    public function __construct(
        public int $amountRequestId,
        public string $oldStatus,
        public string $newStatus,
        public int $userId,
        public array $additionalData = []
    ) {}
}
