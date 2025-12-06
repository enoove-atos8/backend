<?php

namespace App\Infrastructure\Services\Financial\ReceiptDataExtractor;

use App\Infrastructure\Services\Atos8\Financial\OCRExtractDataBankReceipt\OCRExtractDataBankReceiptService;
use App\Infrastructure\Services\Financial\Interfaces\ReceiptDataExtractorInterface;
use App\Infrastructure\Services\Financial\LLMExtractDataBankReceipt\LLMExtractDataBankReceiptService;
use Exception;
use Illuminate\Support\Facades\Log;

class ReceiptDataExtractorWithFallback implements ReceiptDataExtractorInterface
{
    private const STATUS_SUCCESS = 'SUCCESS';

    private const STATUS_READING_ERROR = 'READING_ERROR';

    private const DEFAULT_INSTITUTION = 'GENERIC';

    private const EXTRACTION_METHOD_LLM = 'LLM';

    private const EXTRACTION_METHOD_OCR = 'OCR';

    /**
     * Padrões de erro que indicam necessidade de fallback para OCR
     */
    private const FALLBACK_ERROR_PATTERNS = [
        'rate limit',
        'quota exceeded',
        'resource exhausted',
        '429',
        '503',
        'timeout',
        'service unavailable',
        'too many requests',
        'temporarily unavailable',
    ];

    public function __construct(
        private LLMExtractDataBankReceiptService $llmService,
        private OCRExtractDataBankReceiptService $ocrService
    ) {}

    /**
     * @param  array{destinationPath: string, fullFileNamePdf: ?string, fileUploaded: string}  $arrFilePath
     * @return array{status: string, msg: string, data: array<string, mixed>}
     */
    public function extractData(array $arrFilePath, string $docType, string $docSubType): array
    {
        $llmResult = $this->tryLlmExtraction($arrFilePath, $docType, $docSubType);

        if ($llmResult['success']) {
            Log::info('ReceiptDataExtractor: Extração via LLM bem-sucedida', [
                'file' => $arrFilePath['fileUploaded'] ?? 'unknown',
            ]);

            return $llmResult['data'];
        }

        if ($this->shouldFallbackToOcr($llmResult['error'])) {
            Log::info('ReceiptDataExtractor: Fazendo fallback para OCR', [
                'reason' => $llmResult['error'],
                'file' => $arrFilePath['fileUploaded'] ?? 'unknown',
            ]);

            return $this->tryOcrExtraction($arrFilePath, $docType, $docSubType);
        }

        return $llmResult['data'];
    }

    /**
     * @param  array{destinationPath: string, fullFileNamePdf: ?string, fileUploaded: string}  $arrFilePath
     * @return array{success: bool, data: array, error: ?string}
     */
    private function tryLlmExtraction(array $arrFilePath, string $docType, string $docSubType): array
    {
        try {
            $result = $this->llmService->extractData($arrFilePath, $docType, $docSubType);

            if ($result['status'] === self::STATUS_SUCCESS) {
                return [
                    'success' => true,
                    'data' => $result,
                    'error' => null,
                ];
            }

            $errorMsg = strtolower($result['msg'] ?? '');

            return [
                'success' => false,
                'data' => $result,
                'error' => $errorMsg,
            ];

        } catch (Exception $e) {
            Log::warning('ReceiptDataExtractor: Erro no LLM', [
                'error' => $e->getMessage(),
                'file' => $arrFilePath['fileUploaded'] ?? 'unknown',
            ]);

            return [
                'success' => false,
                'data' => $this->buildErrorResponse($docType, $docSubType, $e->getMessage()),
                'error' => strtolower($e->getMessage()),
            ];
        }
    }

    /**
     * @param  array{destinationPath: string, fullFileNamePdf: ?string, fileUploaded: string}  $arrFilePath
     * @return array{status: string, msg: string, data: array<string, mixed>}
     */
    private function tryOcrExtraction(array $arrFilePath, string $docType, string $docSubType): array
    {
        try {
            $result = $this->ocrService->ocrExtractData($arrFilePath, $docType, $docSubType);

            if (is_array($result)) {
                Log::info('ReceiptDataExtractor: Extração via OCR bem-sucedida', [
                    'file' => $arrFilePath['fileUploaded'] ?? 'unknown',
                ]);

                return $result;
            }

            return $this->buildErrorResponse($docType, $docSubType, 'OCR retornou formato inesperado', self::EXTRACTION_METHOD_OCR);

        } catch (Exception $e) {
            Log::error('ReceiptDataExtractor: Erro no OCR (fallback)', [
                'error' => $e->getMessage(),
                'file' => $arrFilePath['fileUploaded'] ?? 'unknown',
            ]);

            return $this->buildErrorResponse($docType, $docSubType, 'Falha no LLM e OCR: '.$e->getMessage(), self::EXTRACTION_METHOD_OCR);
        }
    }

    private function shouldFallbackToOcr(?string $error): bool
    {
        if (empty($error)) {
            return false;
        }

        foreach (self::FALLBACK_ERROR_PATTERNS as $pattern) {
            if (str_contains($error, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{status: string, msg: string, data: array<string, mixed>}
     */
    private function buildErrorResponse(string $docType, string $docSubType, string $message, string $extractionMethod = self::EXTRACTION_METHOD_LLM): array
    {
        return [
            'status' => self::STATUS_READING_ERROR,
            'msg' => $message,
            'data' => [
                'name' => '',
                'doc_type' => $docType,
                'doc_sub_type' => $docSubType,
                'amount' => 0,
                'date' => '',
                'cpf' => '',
                'middle_cpf' => '',
                'cnpj' => '',
                'institution' => self::DEFAULT_INSTITUTION,
                'timestamp_value_cpf' => '',
                'extraction_method' => $extractionMethod,
            ],
        ];
    }
}
