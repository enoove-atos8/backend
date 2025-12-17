<?php

declare(strict_types=1);

namespace Application\Core\Enums;

enum PlanType: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';
    case Diamond = 'diamond';

    /**
     * Retorna o ID do plano no banco de dados.
     */
    public function id(): int
    {
        return match ($this) {
            self::Bronze => 1,
            self::Silver => 2,
            self::Gold => 3,
            self::Diamond => 4,
        };
    }

    /**
     * Retorna o limite de membros do plano.
     */
    public function membersLimit(): ?int
    {
        return match ($this) {
            self::Bronze => 100,
            self::Silver => 250,
            self::Gold => 399,
            self::Diamond => null, // ilimitado
        };
    }

    /**
     * Retorna o limite de armazenamento em GB.
     */
    public function storageGb(): int
    {
        return match ($this) {
            self::Bronze => 10,
            self::Silver => 50,
            self::Gold => 100,
            self::Diamond => 500,
        };
    }

    /**
     * Verifica se o plano tem relatórios avançados.
     */
    public function hasAdvancedReports(): bool
    {
        return match ($this) {
            self::Bronze => false,
            self::Silver, self::Gold, self::Diamond => true,
        };
    }

    /**
     * Verifica se o plano tem suporte prioritário.
     */
    public function hasPrioritySupport(): bool
    {
        return match ($this) {
            self::Bronze, self::Silver => false,
            self::Gold, self::Diamond => true,
        };
    }

    /**
     * Verifica se este plano é igual ou superior ao plano informado.
     */
    public function isAtLeast(PlanType $plan): bool
    {
        return $this->id() >= $plan->id();
    }

    /**
     * Cria um PlanType a partir do ID.
     */
    public static function fromId(int $id): ?self
    {
        return match ($id) {
            1 => self::Bronze,
            2 => self::Silver,
            3 => self::Gold,
            4 => self::Diamond,
            default => null,
        };
    }

    /**
     * Cria um PlanType a partir do nome.
     */
    public static function fromName(string $name): ?self
    {
        return match (strtolower($name)) {
            'bronze' => self::Bronze,
            'silver' => self::Silver,
            'gold' => self::Gold,
            'diamond' => self::Diamond,
            default => null,
        };
    }
}
