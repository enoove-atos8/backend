<?php

namespace App\Domain\Onboarding\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class OnboardingStepData extends DataTransferObject
{
    public int $step;

    public string $name;

    public string $title;

    public string $description;

    public bool $completed;

    public bool $required;

    public int $count;

    public ?int $minimumRequired;

    public ?bool $skippable;

    public ?array $details;

    public static function fromResponse(array $data): self
    {
        return new self(
            step: $data['step'],
            name: $data['name'],
            title: $data['title'],
            description: $data['description'],
            completed: $data['completed'],
            required: $data['required'],
            count: $data['count'] ?? 0,
            minimumRequired: $data['minimum_required'] ?? null,
            skippable: $data['skippable'] ?? null,
            details: $data['details'] ?? null,
        );
    }
}
