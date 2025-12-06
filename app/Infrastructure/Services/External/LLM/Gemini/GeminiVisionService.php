<?php

namespace App\Infrastructure\Services\External\LLM\Gemini;

use App\Infrastructure\Services\External\LLM\Contracts\LlmVisionServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiVisionService implements LlmVisionServiceInterface
{
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    private const TEMPERATURE = 0.1;

    private const MAX_TOKENS = 1000;

    private const PROVIDER_NAME = 'Gemini Vision';

    private const TIMEOUT_SECONDS = 60;

    public const ERROR_API_KEY_NOT_CONFIGURED = 'API key do Gemini não configurada';

    public const ERROR_API_COMMUNICATION = 'Erro ao comunicar com a API do Gemini';

    public const ERROR_EMPTY_RESPONSE = 'Resposta vazia da API do Gemini';

    public const ERROR_FILE_NOT_FOUND = 'Arquivo de imagem não encontrado';

    public const ERROR_INVALID_JSON = 'Resposta do Gemini não é um JSON válido';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
    }

    public function getProviderName(): string
    {
        return self::PROVIDER_NAME;
    }

    public function processImage(string $imagePath, string $prompt): string
    {
        $this->validateApiKey();
        $this->validateFile($imagePath);

        $imageData = $this->prepareImageData($imagePath);

        return $this->callApi($prompt, $imageData);
    }

    /**
     * @return array<string, mixed>
     */
    public function processImageAsJson(string $imagePath, string $prompt): array
    {
        $response = $this->processImage($imagePath, $prompt);

        return $this->parseJsonResponse($response);
    }

    private function validateApiKey(): void
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException(self::ERROR_API_KEY_NOT_CONFIGURED);
        }
    }

    private function validateFile(string $filePath): void
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException(self::ERROR_FILE_NOT_FOUND.': '.$filePath);
        }
    }

    /**
     * @return array{mimeType: string, data: string}
     */
    private function prepareImageData(string $imagePath): array
    {
        $mimeType = $this->getMimeType($imagePath);
        $imageContent = file_get_contents($imagePath);
        $base64Image = base64_encode($imageContent);

        return [
            'mimeType' => $mimeType,
            'data' => $base64Image,
        ];
    }

    private function getMimeType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            default => 'image/jpeg',
        };
    }

    /**
     * @param array{mimeType: string, data: string} $imageData
     */
    private function callApi(string $prompt, array $imageData): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(self::TIMEOUT_SECONDS)->post(self::API_URL.'?key='.$this->apiKey, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                        [
                            'inline_data' => [
                                'mime_type' => $imageData['mimeType'],
                                'data' => $imageData['data'],
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => self::TEMPERATURE,
                'maxOutputTokens' => self::MAX_TOKENS,
            ],
        ]);

        if (! $response->successful()) {
            Log::error('Gemini Vision API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException(self::ERROR_API_COMMUNICATION.': '.$response->status());
        }

        $content = $response->json('candidates.0.content.parts.0.text');

        if (empty($content)) {
            throw new RuntimeException(self::ERROR_EMPTY_RESPONSE);
        }

        return $content;
    }

    /**
     * @return array<string, mixed>
     */
    private function parseJsonResponse(string $response): array
    {
        $json = preg_replace('/```json\s*/i', '', $response);
        $json = preg_replace('/```\s*/', '', $json);
        $json = trim($json);

        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            Log::warning('Gemini Vision: resposta não é JSON válido', [
                'response' => $response,
            ]);

            throw new RuntimeException(self::ERROR_INVALID_JSON);
        }

        return $decoded;
    }
}
