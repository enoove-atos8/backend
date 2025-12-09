<?php

namespace App\Infrastructure\Services\Financial\LLMExtractDataBankReceipt;

use App\Infrastructure\Services\External\LLM\Contracts\LlmVisionServiceInterface;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class LLMExtractDataBankReceiptService
{
    private const PROMPT_PATH = 'prompts/financial/receipt_extraction.txt';

    private const ENTRIES_RECEIPT = 'entries';

    private const EXITS_RECEIPT = 'exits';

    private const TITHE_ENTRY_TYPE = 'tithe';

    private const DESIGNATED_ENTRY_TYPE = 'designated';

    private const OFFER_ENTRY_TYPE = 'offer';

    private const ACCOUNTS_TRANSFER_ENTRY_TYPE = 'accounts_transfer';

    private const STATUS_SUCCESS = 'SUCCESS';

    private const STATUS_READING_ERROR = 'READING_ERROR';

    private const KEY_STATUS = 'status';

    private const KEY_MSG = 'msg';

    private const KEY_DATA = 'data';

    private const KEY_NAME = 'name';

    private const KEY_DOC_TYPE = 'doc_type';

    private const KEY_DOC_SUB_TYPE = 'doc_sub_type';

    private const KEY_AMOUNT = 'amount';

    private const KEY_DATE = 'date';

    private const KEY_CPF = 'cpf';

    private const KEY_MIDDLE_CPF = 'middle_cpf';

    private const KEY_CNPJ = 'cnpj';

    private const KEY_INSTITUTION = 'institution';

    private const KEY_TIMESTAMP_VALUE_CPF = 'timestamp_value_cpf';

    private const KEY_EXTRACTION_METHOD = 'extraction_method';

    private const KEY_TRANSACTION_ID = 'transaction_id';

    private const EXTRACTION_METHOD_LLM = 'LLM';

    private const DEFAULT_TIME = '00:00:00';

    private const DEFAULT_INSTITUTION = 'GENERIC';

    public function __construct(
        private LlmVisionServiceInterface $llmVisionService
    ) {}

    /**
     * Extrai dados do comprovante bancário usando LLM
     *
     * @param array{destinationPath: string, fullFileNamePdf: ?string, fileUploaded: string} $arrFilePath
     * @return array{status: string, msg: string, data: array<string, mixed>}
     */
    public function extractData(array $arrFilePath, string $docType, string $docSubType): array
    {
        try {
            $imagePath = $this->getImagePath($arrFilePath);
            $prompt = $this->loadPrompt();

            $extractedData = $this->llmVisionService->processImageAsJson($imagePath, $prompt);

            if (isset($extractedData['error']) && $extractedData['error'] === true) {
                return $this->buildErrorResponse($docType, $docSubType, $extractedData['message'] ?? 'Erro na extração');
            }

            return $this->buildSuccessResponse($extractedData, $docType, $docSubType);

        } catch (RuntimeException $e) {
            Log::error('LLMExtractDataBankReceiptService: Erro na extração', [
                'error' => $e->getMessage(),
                'file' => $arrFilePath['fileUploaded'] ?? 'unknown',
            ]);

            return $this->buildErrorResponse($docType, $docSubType, $e->getMessage());
        }
    }

    /**
     * @param array{destinationPath: string, fullFileNamePdf: ?string, fileUploaded: string} $arrFilePath
     */
    private function getImagePath(array $arrFilePath): string
    {
        // Prioriza PDF se existir, senão usa a imagem
        if (! empty($arrFilePath['fullFileNamePdf']) && file_exists($arrFilePath['fullFileNamePdf'])) {
            return $arrFilePath['fullFileNamePdf'];
        }

        return $arrFilePath['destinationPath'];
    }

    private function loadPrompt(): string
    {
        $promptPath = resource_path(self::PROMPT_PATH);

        if (! file_exists($promptPath)) {
            throw new RuntimeException('Arquivo de prompt não encontrado: '.self::PROMPT_PATH);
        }

        return file_get_contents($promptPath);
    }

    /**
     * @param array<string, mixed> $extractedData
     * @return array{status: string, msg: string, data: array<string, mixed>}
     */
    private function buildSuccessResponse(array $extractedData, string $docType, string $docSubType): array
    {
        $amount = (int) ($extractedData[self::KEY_AMOUNT] ?? 0);
        $date = $extractedData[self::KEY_DATE] ?? '';
        $time = $extractedData['time'] ?? self::DEFAULT_TIME;
        $cpf = $extractedData[self::KEY_CPF] ?? '';
        $institution = strtoupper($extractedData[self::KEY_INSTITUTION] ?? self::DEFAULT_INSTITUTION);
        $transactionId = $extractedData[self::KEY_TRANSACTION_ID] ?? '';

        // Monta o timestamp_value_cpf
        $timestampValueCpf = $this->buildTimestampValueCpf($date, $time, $amount, $cpf, $transactionId, $docType, $docSubType);

        // Valida se os dados mínimos foram extraídos
        $status = $this->validateExtractedData($amount, $date, $timestampValueCpf, $cpf, $docType, $docSubType);

        return [
            self::KEY_STATUS => $status,
            self::KEY_MSG => '',
            self::KEY_DATA => [
                self::KEY_NAME => '',
                self::KEY_DOC_TYPE => $docType,
                self::KEY_DOC_SUB_TYPE => $docSubType,
                self::KEY_AMOUNT => $amount,
                self::KEY_DATE => $date,
                self::KEY_CPF => '',
                self::KEY_MIDDLE_CPF => $cpf,
                self::KEY_CNPJ => '',
                self::KEY_INSTITUTION => $institution,
                self::KEY_TIMESTAMP_VALUE_CPF => $timestampValueCpf,
                self::KEY_EXTRACTION_METHOD => self::EXTRACTION_METHOD_LLM,
            ],
        ];
    }

    private function buildTimestampValueCpf(
        string $date,
        string $time,
        int $amount,
        string $cpf,
        string $transactionId,
        string $docType,
        string $docSubType
    ): string {
        if (empty($date)) {
            return '';
        }

        // Remove caracteres não numéricos da data e hora
        $dateNumeric = preg_replace('/\D/', '', $date);
        $timeNumeric = preg_replace('/\D/', '', $time);

        $timestamp = $dateNumeric.$timeNumeric.'_'.$amount;

        // Adiciona CPF apenas para dízimos
        if ($docType === self::ENTRIES_RECEIPT && $docSubType === self::TITHE_ENTRY_TYPE && ! empty($cpf)) {
            $timestamp .= '_'.$cpf;
        }

        // Se não houver horário real (00:00:00) e houver transaction_id, adiciona para diferenciar comprovantes
        // Isso é importante para comprovantes do mesmo valor e data mas sem horário específico
        if ($time === self::DEFAULT_TIME && ! empty($transactionId)) {
            // Remove caracteres especiais do transaction_id para manter apenas alfanuméricos
            $cleanTransactionId = preg_replace('/[^a-zA-Z0-9]/', '', $transactionId);
            $timestamp .= '_'.$cleanTransactionId;
        }

        return $timestamp;
    }

    private function validateExtractedData(
        int $amount,
        string $date,
        string $timestampValueCpf,
        string $cpf,
        string $docType,
        string $docSubType
    ): string {
        if ($docType === self::ENTRIES_RECEIPT) {
            if ($docSubType === self::TITHE_ENTRY_TYPE) {
                // Para dízimo: amount e date são obrigatórios
                if ($amount !== 0 && ! empty($date)) {
                    return self::STATUS_SUCCESS;
                }
            } elseif (in_array($docSubType, [self::DESIGNATED_ENTRY_TYPE, self::OFFER_ENTRY_TYPE, self::ACCOUNTS_TRANSFER_ENTRY_TYPE])) {
                // Para outros tipos: amount, date e timestamp são obrigatórios
                if ($amount !== 0 && ! empty($date) && ! empty($timestampValueCpf)) {
                    return self::STATUS_SUCCESS;
                }
            }
        } elseif ($docType === self::EXITS_RECEIPT) {
            // Para saídas: amount e date são obrigatórios
            if ($amount !== 0 && ! empty($date)) {
                return self::STATUS_SUCCESS;
            }
        }

        return self::STATUS_READING_ERROR;
    }

    /**
     * @return array{status: string, msg: string, data: array<string, mixed>}
     */
    private function buildErrorResponse(string $docType, string $docSubType, string $message = ''): array
    {
        return [
            self::KEY_STATUS => self::STATUS_READING_ERROR,
            self::KEY_MSG => $message,
            self::KEY_DATA => [
                self::KEY_NAME => '',
                self::KEY_DOC_TYPE => $docType,
                self::KEY_DOC_SUB_TYPE => $docSubType,
                self::KEY_AMOUNT => 0,
                self::KEY_DATE => '',
                self::KEY_CPF => '',
                self::KEY_MIDDLE_CPF => '',
                self::KEY_CNPJ => '',
                self::KEY_INSTITUTION => self::DEFAULT_INSTITUTION,
                self::KEY_TIMESTAMP_VALUE_CPF => '',
                self::KEY_EXTRACTION_METHOD => self::EXTRACTION_METHOD_LLM,
            ],
        ];
    }
}
