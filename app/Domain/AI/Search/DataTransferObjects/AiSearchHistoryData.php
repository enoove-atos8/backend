<?php

namespace App\Domain\AI\Search\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AiSearchHistoryData extends DataTransferObject
{
    public ?int $id;

    public int $userId;

    public string $question;

    public ?string $sqlGenerated;

    public ?array $resultData;

    public ?string $resultTitle;

    public ?string $resultDescription;

    public ?string $suggestedFollowup;

    public int $executionTimeMs;

    public bool $success;

    public ?string $errorMessage;

    public bool $rateLimitExceeded;

    public ?string $llmProvider;

    public ?string $createdAt;

    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            userId: $data['user_id'] ?? 0,
            question: $data['question'] ?? '',
            sqlGenerated: $data['sql_generated'] ?? null,
            resultData: isset($data['result_data']) ? (is_array($data['result_data']) ? $data['result_data'] : json_decode($data['result_data'], true)) : null,
            resultTitle: $data['result_title'] ?? null,
            resultDescription: $data['result_description'] ?? null,
            suggestedFollowup: $data['suggested_followup'] ?? null,
            executionTimeMs: $data['execution_time_ms'] ?? 0,
            success: (bool) ($data['success'] ?? true),
            errorMessage: $data['error_message'] ?? null,
            rateLimitExceeded: (bool) ($data['rate_limit_exceeded'] ?? false),
            llmProvider: $data['llm_provider'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }
}
