<?php

namespace App\Domain\AI\Search\Services;

use App\Domain\AI\Search\Exceptions\RateLimitExceededException;
use App\Domain\AI\Search\Interfaces\LlmServiceInterface;
use Illuminate\Support\Facades\Log;

class LlmServiceWithFallback implements LlmServiceInterface
{
    private GroqApiService $primaryService;

    private GeminiApiService $fallbackService;

    private ?string $lastUsedProvider = null;

    public function __construct(
        GroqApiService $primaryService,
        GeminiApiService $fallbackService
    ) {
        $this->primaryService = $primaryService;
        $this->fallbackService = $fallbackService;
    }

    public function getProviderName(): string
    {
        return $this->lastUsedProvider ?? $this->primaryService->getProviderName();
    }

    public function generateSql(string $question, string $schema): string
    {
        try {
            $result = $this->primaryService->generateSql($question, $schema);
            $this->lastUsedProvider = $this->primaryService->getProviderName();

            return $result;
        } catch (RateLimitExceededException $e) {
            Log::info('LLM Fallback: Groq rate limit exceeded, switching to Gemini', [
                'question' => $question,
            ]);

            $result = $this->fallbackService->generateSql($question, $schema);
            $this->lastUsedProvider = $this->fallbackService->getProviderName();

            return $result;
        }
    }

    /**
     * @param  array<mixed>  $data
     * @return array{title: string, description: string, suggested_followup: string}
     */
    public function formatResponse(string $question, array $data): array
    {
        try {
            $result = $this->primaryService->formatResponse($question, $data);
            $this->lastUsedProvider = $this->primaryService->getProviderName();

            return $result;
        } catch (RateLimitExceededException $e) {
            Log::info('LLM Fallback: Groq rate limit exceeded, switching to Gemini for format', [
                'question' => $question,
            ]);

            $result = $this->fallbackService->formatResponse($question, $data);
            $this->lastUsedProvider = $this->fallbackService->getProviderName();

            return $result;
        }
    }
}
