<?php

namespace App\Domain\AI\Search\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqApiService
{
    private const API_URL = 'https://api.groq.com/openai/v1/chat/completions';

    private const MODEL = 'llama-3.3-70b-versatile';

    private const TEMPERATURE = 0.1;

    private const MAX_TOKENS = 500;

    private const PROMPT_SQL_PATH = 'prompts/ai_search_system.txt';

    private const PROMPT_FORMAT_PATH = 'prompts/ai_search_format_response.txt';

    private const PLACEHOLDER_SCHEMA = '{{SCHEMA}}';

    private const PLACEHOLDER_QUESTION = '{{QUESTION}}';

    private const PLACEHOLDER_DATA = '{{DATA}}';

    private const DEFAULT_TITLE = 'Resultado';

    private const DEFAULT_DESCRIPTION = 'Consulta realizada com sucesso.';

    private const DEFAULT_FOLLOWUP = '';

    public const ERROR_API_KEY_NOT_CONFIGURED = 'API key do Groq não configurada';

    public const ERROR_API_COMMUNICATION = 'Erro ao comunicar com a API de IA';

    public const ERROR_EMPTY_RESPONSE = 'Resposta vazia da API de IA';

    public const ERROR_PROMPT_NOT_FOUND = 'Arquivo de prompt não encontrado';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
    }

    public function generateSql(string $question, string $schema): string
    {
        $this->validateApiKey();

        $systemPrompt = $this->loadPrompt(self::PROMPT_SQL_PATH);
        $systemPrompt = str_replace(self::PLACEHOLDER_SCHEMA, $schema, $systemPrompt);

        $content = $this->callApi($systemPrompt, $question);

        return $this->extractSqlFromResponse($content);
    }

    /**
     * @param  array<mixed>  $data
     * @return array{title: string, description: string, suggested_followup: string}
     */
    public function formatResponse(string $question, array $data): array
    {
        $this->validateApiKey();

        $systemPrompt = $this->loadPrompt(self::PROMPT_FORMAT_PATH);
        $systemPrompt = str_replace(self::PLACEHOLDER_QUESTION, $question, $systemPrompt);
        $systemPrompt = str_replace(self::PLACEHOLDER_DATA, json_encode($data), $systemPrompt);

        $content = $this->callApi($systemPrompt, $question);

        return $this->parseJsonResponse($content);
    }

    private function validateApiKey(): void
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException(self::ERROR_API_KEY_NOT_CONFIGURED);
        }
    }

    private function loadPrompt(string $path): string
    {
        $promptPath = resource_path($path);

        if (! file_exists($promptPath)) {
            throw new \RuntimeException(self::ERROR_PROMPT_NOT_FOUND);
        }

        return file_get_contents($promptPath);
    }

    private function callApi(string $systemPrompt, string $userMessage): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post(self::API_URL, [
            'model' => self::MODEL,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'temperature' => self::TEMPERATURE,
            'max_tokens' => self::MAX_TOKENS,
        ]);

        if (! $response->successful()) {
            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException(self::ERROR_API_COMMUNICATION);
        }

        $content = $response->json('choices.0.message.content');

        if (empty($content)) {
            throw new \RuntimeException(self::ERROR_EMPTY_RESPONSE);
        }

        return $content;
    }

    private function extractSqlFromResponse(string $response): string
    {
        $sql = preg_replace('/```sql\s*/i', '', $response);
        $sql = preg_replace('/```\s*/', '', $sql);
        $sql = trim($sql);
        $sql = rtrim($sql, ';');

        return $sql;
    }

    /**
     * @return array{title: string, description: string, suggested_followup: string}
     */
    private function parseJsonResponse(string $response): array
    {
        $json = preg_replace('/```json\s*/i', '', $response);
        $json = preg_replace('/```\s*/', '', $json);
        $json = trim($json);

        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            return [
                'title' => self::DEFAULT_TITLE,
                'description' => self::DEFAULT_DESCRIPTION,
                'suggested_followup' => self::DEFAULT_FOLLOWUP,
            ];
        }

        return [
            'title' => $decoded['title'] ?? self::DEFAULT_TITLE,
            'description' => $decoded['description'] ?? self::DEFAULT_DESCRIPTION,
            'suggested_followup' => $decoded['suggested_followup'] ?? self::DEFAULT_FOLLOWUP,
        ];
    }
}
