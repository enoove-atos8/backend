<?php

namespace App\Domain\AI\Search\Services;

use App\Domain\AI\Search\Exceptions\RateLimitExceededException;
use App\Domain\AI\Search\Interfaces\LlmServiceInterface;
use Illuminate\Support\Facades\Log;

class LlmServiceWithFallback implements LlmServiceInterface
{
    private GroqApiService $primaryService;

    private GeminiApiService $fallbackService;

    public function __construct(
        GroqApiService $primaryService,
        GeminiApiService $fallbackService
    ) {
        $this->primaryService = $primaryService;
        $this->fallbackService = $fallbackService;
    }

    public function generateSql(string $question, string $schema): string
    {
        try {
            return $this->primaryService->generateSql($question, $schema);
        } catch (RateLimitExceededException $e) {
            Log::info('LLM Fallback: Groq rate limit exceeded, switching to Gemini', [
                'question' => $question,
            ]);

            return $this->fallbackService->generateSql($question, $schema);
        }
    }

    /**
     * @param  array<mixed>  $data
     * @return array{title: string, description: string, suggested_followup: string}
     */
    public function formatResponse(string $question, array $data): array
    {
        try {
            return $this->primaryService->formatResponse($question, $data);
        } catch (RateLimitExceededException $e) {
            Log::info('LLM Fallback: Groq rate limit exceeded, switching to Gemini for format', [
                'question' => $question,
            ]);

            return $this->fallbackService->formatResponse($question, $data);
        }
    }
}
