<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Constants;

class ReturnMessages
{
    // Success messages
    public const AMOUNT_REQUEST_CREATED = 'Solicitação de verba criada com sucesso!';

    public const AMOUNT_REQUEST_UPDATED = 'Solicitação de verba atualizada com sucesso!';

    public const AMOUNT_REQUEST_DELETED = 'Solicitação de verba excluída com sucesso!';

    public const AMOUNT_REQUEST_APPROVED = 'Solicitação de verba aprovada com sucesso!';

    public const AMOUNT_REQUEST_REJECTED = 'Solicitação de verba rejeitada com sucesso!';

    public const AMOUNT_REQUEST_TRANSFERRED = 'Valor transferido com sucesso!';

    public const EXIT_LINKED = 'Saída vinculada à solicitação com sucesso!';

    public const EXIT_UNLINKED = 'Saída desvinculada da solicitação com sucesso!';

    public const AMOUNT_REQUEST_CLOSED = 'Solicitação de verba fechada com sucesso!';

    public const RECEIPT_CREATED = 'Comprovante adicionado com sucesso!';

    public const RECEIPT_UPDATED = 'Comprovante atualizado com sucesso!';

    public const RECEIPT_DELETED = 'Comprovante removido com sucesso!';

    public const REMINDER_CREATED = 'Lembrete criado com sucesso!';

    // Error messages
    public const AMOUNT_REQUEST_NOT_FOUND = 'Solicitação de verba não encontrada!';

    public const ERROR_CREATE_AMOUNT_REQUEST = 'Erro ao criar solicitação de verba!';

    public const ERROR_UPDATE_AMOUNT_REQUEST = 'Erro ao atualizar solicitação de verba!';

    public const ERROR_DELETE_AMOUNT_REQUEST = 'Erro ao excluir solicitação de verba!';

    public const ERROR_APPROVE_AMOUNT_REQUEST = 'Erro ao aprovar solicitação de verba!';

    public const ERROR_REJECT_AMOUNT_REQUEST = 'Erro ao rejeitar solicitação de verba!';

    public const ERROR_TRANSFER_AMOUNT_REQUEST = 'Erro ao transferir valor!';

    public const ERROR_CLOSE_AMOUNT_REQUEST = 'Erro ao fechar solicitação de verba!';

    public const ERROR_CREATE_RECEIPT = 'Erro ao adicionar comprovante!';

    public const ERROR_DELETE_RECEIPT = 'Erro ao remover comprovante!';

    public const ERROR_UPDATE_RECEIPT = 'Erro ao atualizar comprovante!';

    public const ERROR_CREATE_REMINDER = 'Erro ao criar lembrete!';

    public const ERROR_UPLOAD_FILE = 'Erro ao fazer upload do arquivo!';

    public const RECEIPT_NOT_FOUND = 'Comprovante não encontrado!';

    // Validation messages
    public const INVALID_STATUS_FOR_APPROVAL = 'Apenas solicitações pendentes podem ser aprovadas!';

    public const INVALID_STATUS_FOR_REJECTION = 'Apenas solicitações pendentes podem ser rejeitadas!';

    public const INVALID_STATUS_FOR_TRANSFER = 'Apenas solicitações aprovadas podem receber transferência!';

    public const INVALID_STATUS_FOR_LINK = 'Apenas solicitações aprovadas podem ser vinculadas a uma saída!';

    public const INVALID_STATUS_FOR_UNLINK = 'Apenas solicitações transferidas podem ser desvinculadas!';

    public const ERROR_LINK_EXIT = 'Erro ao vincular saída à solicitação!';

    public const ERROR_UNLINK_EXIT = 'Erro ao desvincular saída da solicitação!';

    public const INVALID_STATUS_FOR_CLOSE = 'Apenas solicitações comprovadas, parcialmente comprovadas ou vencidas podem ser fechadas!';

    public const INVALID_STATUS_FOR_RECEIPT = 'Não é possível adicionar comprovantes neste status!';

    public const REJECTION_REASON_REQUIRED = 'O motivo da rejeição é obrigatório!';

    public const GROUP_HAS_OPEN_REQUEST = 'Este grupo já possui uma solicitação de verba em andamento!';

    public const GROUP_INSUFFICIENT_BALANCE = 'O grupo não possui saldo suficiente para esta solicitação!';

    public const GROUP_LIMIT_EXCEEDED = 'O valor solicitado excede o limite de Investimento Ministerial configurado para este grupo!';

    public const GROUP_NO_MINISTERIAL_LIMIT = 'Este grupo não possui limite configurado para Investimento Ministerial!';

    public const INVALID_REQUEST_TYPE = 'Tipo de solicitação inválido!';

    // Type constants
    public const TYPE_GROUP_FUND = 'group_fund';

    public const TYPE_MINISTERIAL_INVESTMENT = 'ministerial_investment';

    // Status constants
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_TRANSFERRED = 'transferred';

    public const STATUS_PARTIALLY_PROVEN = 'partially_proven';

    public const STATUS_PROVEN = 'proven';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_OVERDUE = 'overdue';

    // Type labels
    public const TYPE_LABELS = [
        self::TYPE_GROUP_FUND => 'Verba de Grupo',
        self::TYPE_MINISTERIAL_INVESTMENT => 'Investimento Ministerial',
    ];

    // Status labels
    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pendente',
        self::STATUS_APPROVED => 'Aprovada',
        self::STATUS_REJECTED => 'Rejeitada',
        self::STATUS_TRANSFERRED => 'Valor Transferido',
        self::STATUS_PARTIALLY_PROVEN => 'Parcialmente Comprovada',
        self::STATUS_PROVEN => 'Comprovada',
        self::STATUS_CLOSED => 'Fechada',
        self::STATUS_OVERDUE => 'Vencida',
    ];

    // Reminder type labels
    public const REMINDER_TYPE_LABELS = [
        'request_created' => 'Solicitação Criada',
        'request_approved' => 'Solicitação Aprovada',
        'request_rejected' => 'Solicitação Rejeitada',
        'transfer_completed' => 'Transferência Realizada',
        'proof_in_progress' => 'Comprovação em Andamento',
        'proof_completed' => 'Comprovação Completa',
        'proof_reminder' => 'Lembrete de Comprovação',
        'proof_urgent' => 'Comprovação Urgente',
        'proof_overdue' => 'Comprovação Vencida',
        'proof_received' => 'Comprovante Recebido',
        'devolution_required' => 'Devolução Necessária',
        'request_closed' => 'Solicitação Fechada',
    ];

    // Channel labels
    public const CHANNEL_LABELS = [
        'whatsapp' => 'WhatsApp',
        'email' => 'E-mail',
        'system' => 'Sistema',
    ];

    // Reminder status labels
    public const REMINDER_STATUS_LABELS = [
        'pending' => 'Pendente',
        'sent' => 'Enviado',
        'failed' => 'Falhou',
        'delivered' => 'Entregue',
        'read' => 'Lido',
    ];

    // History event constants
    public const HISTORY_EVENT_CREATED = 'created';

    public const HISTORY_EVENT_APPROVED = 'approved';

    public const HISTORY_EVENT_REJECTED = 'rejected';

    public const HISTORY_EVENT_TRANSFERRED = 'transferred';

    public const HISTORY_EVENT_EXIT_UNLINKED = 'exit_unlinked';

    public const HISTORY_EVENT_RECEIPT_ADDED = 'receipt_added';

    public const HISTORY_EVENT_RECEIPT_UPDATED = 'receipt_updated';

    public const HISTORY_EVENT_RECEIPT_DELETED = 'receipt_deleted';

    public const HISTORY_EVENT_CLOSED = 'closed';

    public const HISTORY_EVENT_DEVOLUTION_LINKED = 'devolution_linked';

    // Auto-close description
    public const AUTO_CLOSE_DESCRIPTION = 'Solicitação fechada automaticamente (comprovado + devolvido = solicitado)';

    // Metadata keys
    public const METADATA_KEY_REQUESTED_AMOUNT = 'requested_amount';

    public const METADATA_KEY_PROVEN_AMOUNT = 'proven_amount';

    public const METADATA_KEY_DEVOLUTION_AMOUNT = 'devolution_amount';

    public const METADATA_KEY_AUTO_CLOSED = 'auto_closed';

    public const METADATA_KEY_ENTRY_ID = 'entry_id';

    public const METADATA_KEY_LINKED_ENTRY_ID = 'linked_entry_id';

    public const METADATA_KEY_GROUP_ID = 'group_id';

    public const METADATA_KEY_EXIT_ID = 'exit_id';

    public const METADATA_KEY_REJECTION_REASON = 'rejection_reason';

    // History event descriptions
    public const HISTORY_DESCRIPTIONS = [
        self::HISTORY_EVENT_CREATED => 'Solicitação de verba criada',
        self::HISTORY_EVENT_APPROVED => 'Solicitação aprovada',
        self::HISTORY_EVENT_REJECTED => 'Solicitação rejeitada',
        self::HISTORY_EVENT_TRANSFERRED => 'Valor transferido',
        self::HISTORY_EVENT_EXIT_UNLINKED => 'Saída desvinculada',
        self::HISTORY_EVENT_RECEIPT_ADDED => 'Comprovante adicionado',
        self::HISTORY_EVENT_RECEIPT_UPDATED => 'Comprovante atualizado',
        self::HISTORY_EVENT_RECEIPT_DELETED => 'Comprovante removido',
        self::HISTORY_EVENT_CLOSED => 'Solicitação fechada',
        self::HISTORY_EVENT_DEVOLUTION_LINKED => 'Devolução registrada',
    ];
}
