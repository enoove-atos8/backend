<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Services;

class WhatsAppMessageTemplateService
{
    /**
     * Templates de mensagens para cada tipo de notificação
     */
    private const TEMPLATES = [
        'request_created' => "🔔 *Solicitação de Verba Criada*\n\nOlá {member_name}!\n\nSua solicitação de verba para *{group_name}* no valor de *R$ {amount}* foi criada com sucesso.\n\n📝 Descrição: {description}\n📅 Prazo para comprovação: {deadline}\n\nAguarde a aprovação.",

        'request_approved' => "✅ *Solicitação Aprovada*\n\nOlá {member_name}!\n\nSua solicitação de verba para *{group_name}* no valor de *R$ {amount}* foi aprovada!\n\n📅 Prazo para comprovação: {deadline}\n\nAguarde a transferência do valor.",

        'request_rejected' => "❌ *Solicitação Rejeitada*\n\nOlá {member_name}!\n\nInfelizmente sua solicitação de verba para *{group_name}* no valor de *R$ {amount}* foi rejeitada.\n\n📋 Motivo: {rejection_reason}\n\nEm caso de dúvidas, entre em contato com a administração.",

        'transfer_completed' => "💰 *Transferência Realizada*\n\nOlá {member_name}!\n\nA transferência de *R$ {amount}* para *{group_name}* foi realizada com sucesso!\n\n📅 Prazo para comprovação: {deadline}\n⚠️ Lembre-se de guardar todos os comprovantes de uso do valor.",

        'proof_in_progress' => "📋 *Comprovação em Andamento*\n\nOlá {member_name}!\n\nRecebemos seus comprovantes de *{group_name}*!\n\n💰 Valor solicitado: R$ {amount}\n✅ Comprovado até agora: R$ {proven_amount}\n❌ Ainda falta comprovar: R$ {pending_amount}\n📅 Prazo: {deadline}\n\nContinue enviando os comprovantes restantes!",

        'proof_completed' => "✅ *Comprovação Completa*\n\nOlá {member_name}!\n\nParabéns! Todos os valores da solicitação de *{group_name}* foram comprovados!\n\n💰 Valor solicitado: R$ {amount}\n✅ Valor comprovado: R$ {proven_amount}\n\nObrigado pela prestação de contas!",

        'proof_reminder' => "📋 *Lembrete de Comprovação*\n\nOlá {member_name}!\n\nLembramos que você recebeu *R$ {amount}* para *{group_name}* e o prazo para comprovação é até *{deadline}*.\n\n✅ Comprovado até agora: R$ {proven_amount}\n⏰ Faltam {days_remaining} dias\n\nPor favor, envie os comprovantes o quanto antes.",

        'proof_urgent' => "⚠️ *URGENTE - Prazo de Comprovação*\n\nOlá {member_name}!\n\n🚨 O prazo para comprovação está próximo do fim!\n\n👥 Grupo: *{group_name}*\n💰 Valor recebido: R$ {amount}\n✅ Comprovado: R$ {proven_amount}\n❌ Pendente: R$ {pending_amount}\n📅 Prazo: {deadline} ({days_remaining} dias)\n\n⚠️ Envie os comprovantes urgentemente para evitar problemas.",

        'proof_overdue' => "🚨 *PRAZO VENCIDO - Comprovação Pendente*\n\nOlá {member_name}!\n\n❌ O prazo para comprovação venceu em {deadline}.\n\n👥 Grupo: *{group_name}*\n💰 Valor recebido: R$ {amount}\n✅ Comprovado: R$ {proven_amount}\n❌ Pendente: R$ {pending_amount}\n\n⚠️ Entre em contato urgentemente com a administração.",

        'proof_received' => "✅ *Comprovação Recebida*\n\nOlá {member_name}!\n\nRecebemos seus comprovantes de *{group_name}*!\n\n💰 Valor total: R$ {amount}\n✅ Comprovado: R$ {proven_amount}\n\nObrigado pela prestação de contas!",

        'devolution_required' => "💸 *Devolução Necessária*\n\nOlá {member_name}!\n\nApós análise dos comprovantes de *{group_name}*, identificamos que é necessário devolver valores.\n\n💰 Valor recebido: R$ {amount}\n✅ Comprovado: R$ {proven_amount}\n💸 A devolver: R$ {devolution_amount}\n\nPor favor, entre em contato com a administração.",

        'request_closed' => "✅ *Solicitação Encerrada*\n\nOlá {member_name}!\n\nSua solicitação de verba para *{group_name}* foi encerrada com sucesso.\n\n💰 Valor solicitado: R$ {amount}\n✅ Valor comprovado: R$ {proven_amount}\n\nObrigado pela prestação de contas!",
    ];

    /**
     * Obtém o template formatado com os dados fornecidos
     *
     * @param  array<string, mixed>  $data
     */
    public function getTemplate(string $type, array $data): string
    {
        if (! isset(self::TEMPLATES[$type])) {
            throw new \InvalidArgumentException("Template '{$type}' não encontrado.");
        }

        $template = self::TEMPLATES[$type];

        foreach ($data as $key => $value) {
            $template = str_replace('{'.$key.'}', (string) $value, $template);
        }

        return $template;
    }

    /**
     * Verifica se um tipo de template existe
     */
    public function hasTemplate(string $type): bool
    {
        return isset(self::TEMPLATES[$type]);
    }

    /**
     * Obtém todos os tipos de templates disponíveis
     *
     * @return array<string>
     */
    public function getAvailableTypes(): array
    {
        return array_keys(self::TEMPLATES);
    }
}
