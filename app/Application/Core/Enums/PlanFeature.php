<?php

declare(strict_types=1);

namespace Application\Core\Enums;

enum PlanFeature: string
{
    case MembersLimit = 'members_limit';
    case StorageGb = 'storage_gb';
    case BasicReports = 'basic_reports';
    case AdvancedReports = 'advanced_reports';
    case PrioritySupport = 'priority_support';
    case CloudRepositoryReceipts = 'cloud_repository_receipts';
    case MeteredBilling = 'metered_billing';

    /**
     * Retorna o plano mínimo necessário para a feature.
     */
    public function minimumPlan(): PlanType
    {
        return match ($this) {
            self::MembersLimit => PlanType::Bronze,
            self::StorageGb => PlanType::Bronze,
            self::BasicReports => PlanType::Bronze,
            self::AdvancedReports => PlanType::Silver,
            self::PrioritySupport => PlanType::Gold,
            self::CloudRepositoryReceipts => PlanType::Diamond,
            self::MeteredBilling => PlanType::Diamond,
        };
    }

    /**
     * Retorna a descrição da feature.
     */
    public function description(): string
    {
        return match ($this) {
            self::MembersLimit => 'Limite de membros cadastrados',
            self::StorageGb => 'Armazenamento em nuvem',
            self::BasicReports => 'Relatórios básicos',
            self::AdvancedReports => 'Relatórios avançados',
            self::PrioritySupport => 'Suporte prioritário',
            self::CloudRepositoryReceipts => 'Repositório de comprovantes em nuvem',
            self::MeteredBilling => 'Cobrança por uso (por membro)',
        };
    }
}
