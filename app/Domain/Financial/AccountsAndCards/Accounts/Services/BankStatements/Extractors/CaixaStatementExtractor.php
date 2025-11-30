<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\Extractors;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Carbon\Carbon;
use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\DataTransferObjects\ExtractorFileData;
use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\Interfaces\BankStatementExtractorInterface;
use Illuminate\Support\Str;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\PdfToText\Pdf;

class CaixaStatementExtractor implements BankStatementExtractorInterface
{
    /**
     * Identificador de depósito em dinheiro no extrato TXT da Caixa
     * Usado para conciliação de cultos (que são sempre em dinheiro)
     */
    public const TXT_CASH_DEPOSIT_IDENTIFIER = 'DIN';

    /**
     * Identificador de saldo diário no extrato PDF da Caixa (deve ser ignorado)
     */
    public const PDF_DAILY_BALANCE_IDENTIFIER = 'SALDO DIA';

    /**
     * Número do documento para linhas de saldo diário
     */
    public const PDF_DAILY_BALANCE_DOC_NUMBER = '000000';

    /**
     * Formatos de PDF suportados pela Caixa
     *
     * FORMAT_1: Saldo em linha separada (formato antigo)
     * FORMAT_2: Saldo na mesma linha da movimentação (formato novo)
     */
    private const PDF_FORMAT_1 = 'format_1';

    private const PDF_FORMAT_2 = 'format_2';

    /**
     * Mensagens de erro para validação de PDF
     */
    private const ERROR_PERIOD_NOT_FOUND_PDF = 'Movement dates not found in PDF file';

    private const ERROR_MONTH_VALIDATION_FAILED = "Month validation failed: Expected month '%s' not found in file movement date '%s'";

    private const ERROR_PDF_TEXT_EXTRACTION = 'Unable to extract text from PDF file: %s';

    /**
     * Extract bank statement data from Caixa file
     *
     * @param  string  $filePath  * @return array
     *
     * @throws GeneralExceptions
     */
    public function extract(string $filePath, AccountFileData $file): array
    {
        return match (strtoupper($file->fileType)) {
            'PDF' => $this->extractFromPdf($filePath, $file),
            'TXT' => $this->extractFromTxt($filePath, $file),
            'OFX' => $this->extractFromOfx($filePath, $file),
            'CSV' => $this->extractFromCsv($filePath, $file),

            default => throw new GeneralExceptions("File type not supported for Caixa: {$file->fileType}", 400)
        };
    }

    /**
     * Validate if the file contains movements for the expected account
     *
     * @throws GeneralExceptions
     */
    private function validateAccountFile(string $filePath, AccountFileData $file, string $fileType): void
    {
        $accountNumber = str_replace('-', '', $file->account->accountNumber);

        match (strtoupper($fileType)) {
            'TXT' => $this->validateTxtAccount($filePath, $accountNumber),
            'PDF' => $this->validatePdfAccount($filePath, $accountNumber),
            'CSV' => $this->validateCsvAccount($filePath, $accountNumber),
            'OFX' => $this->validateOfxAccount($filePath, $accountNumber),

            default => throw new GeneralExceptions("File type validation not supported: {$fileType}", 400)
        };
    }

    /**
     * Validate TXT file account
     *
     * @throws GeneralExceptions
     */
    private function validateTxtAccount(string $filePath, string $accountNumber): void
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new GeneralExceptions("Unable to open file for validation: {$filePath}", 500);
        }

        fgetcsv($handle, 0, ';');
        $firstRow = fgetcsv($handle, 0, ';');
        fclose($handle);

        if ($firstRow === false || empty($firstRow[0])) {
            throw new GeneralExceptions('File is empty or has invalid format', 422);
        }

        $accountInFile = trim($firstRow[0], '"');

        if (! Str::contains($accountInFile, $accountNumber)) {
            throw new GeneralExceptions(
                "Account validation failed: Expected account '{$accountNumber}' not found in file data '{$accountInFile}'",
                422
            );
        }
    }

    /**
     * Validate if the file movements match the expected reference month
     *
     * @throws GeneralExceptions
     */
    private function validateMonthFile(string $filePath, AccountFileData $file, string $fileType): void
    {
        $referenceMonth = str_replace('-', '', $file->referenceDate);

        match (strtoupper($fileType)) {
            'TXT' => $this->validateTxtMonth($filePath, $referenceMonth),
            'PDF' => $this->validatePdfMonth($filePath, $referenceMonth),
            'CSV' => $this->validateCsvMonth($filePath, $referenceMonth),
            'OFX' => $this->validateOfxMonth($filePath, $referenceMonth),

            default => throw new GeneralExceptions("File type validation not supported: {$fileType}", 400)
        };
    }

    /**
     * Validate TXT file month
     *
     * @throws GeneralExceptions
     */
    private function validateTxtMonth(string $filePath, string $referenceMonth): void
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new GeneralExceptions("Unable to open file for validation: {$filePath}", 500);
        }

        fgetcsv($handle, 0, ';');
        $firstRow = fgetcsv($handle, 0, ';');
        fclose($handle);

        if ($firstRow === false || empty($firstRow[1])) {
            throw new GeneralExceptions('File is empty or has invalid format', 423);
        }

        $movementDateInFile = trim($firstRow[1], '"');

        if (! Str::contains($movementDateInFile, $referenceMonth)) {
            throw new GeneralExceptions(
                "Month validation failed: Expected month '{$referenceMonth}' not found in file movement date '{$movementDateInFile}'",
                423
            );
        }
    }

    /**
     * Validate PDF file month
     *
     * Valida o mês do extrato baseado nas datas das movimentações encontradas.
     *
     * @throws GeneralExceptions
     */
    private function validatePdfMonth(string $filePath, string $referenceMonth): void
    {
        $text = $this->extractTextFromPdf($filePath);

        // Busca a primeira data de movimentação no PDF
        // Formato esperado: "DD/MM/YYYY" no início da linha
        if (! preg_match('/^(\d{2}\/\d{2}\/\d{4})\s+\d{6}/m', $text, $matches)) {
            throw new GeneralExceptions(self::ERROR_PERIOD_NOT_FOUND_PDF, 423);
        }

        // Extrai ano e mês da primeira movimentação (formato: DD/MM/YYYY)
        $firstMovementDate = $matches[1];
        $dateParts = explode('/', $firstMovementDate);
        $yearMonthInFile = $dateParts[2] . $dateParts[1]; // YYYYMM

        if ($yearMonthInFile !== $referenceMonth) {
            throw new GeneralExceptions(
                sprintf(self::ERROR_MONTH_VALIDATION_FAILED, $referenceMonth, $firstMovementDate),
                423
            );
        }
    }

    /**
     * Validate CSV file month
     */
    private function validateCsvMonth(string $filePath, string $referenceMonth): void
    {
        // TODO: Implement CSV month validation logic for Caixa
        // Extract movement dates from CSV and validate
    }

    /**
     * Validate OFX file month
     */
    private function validateOfxMonth(string $filePath, string $referenceMonth): void
    {
        // TODO: Implement OFX month validation logic for Caixa
        // Extract movement dates from OFX and validate
    }

    /**
     * Validate PDF file account
     *
     * O extrato PDF da Caixa não possui cabeçalho com número da conta.
     * A validação é ignorada e confiamos que o usuário informou a conta correta.
     */
    private function validatePdfAccount(string $filePath, string $accountNumber): void
    {
        // PDF não possui informação de conta no cabeçalho - validação não aplicável
    }

    /**
     * Validate CSV file account
     */
    private function validateCsvAccount(string $filePath, string $accountNumber): void
    {
        // TODO: Implement CSV account validation logic for Caixa
        // Extract account information from CSV and validate
    }

    /**
     * Validate OFX file account
     */
    private function validateOfxAccount(string $filePath, string $accountNumber): void
    {
        // TODO: Implement OFX account validation logic for Caixa
        // Extract account information from OFX and validate
    }

    /**
     * Extract data from PDF format
     *
     * @throws GeneralExceptions
     */
    private function extractFromPdf(string $filePath, AccountFileData $file): array
    {
        $this->validateAccountFile($filePath, $file, 'PDF');
        $this->validateMonthFile($filePath, $file, 'PDF');

        $text = $this->extractTextFromPdf($filePath);
        $lines = explode("\n", $text);

        // Detecta o formato do PDF baseado no cabeçalho
        $pdfFormat = $this->detectPdfFormat($text);

        // Extrai o número da conta do cabeçalho para usar nos dados extraídos
        preg_match('/Conta\s+(\d{4}\s*\/\s*[\d.\-]+)/i', $text, $accountMatch);
        $accountFromFile = $accountMatch[1] ?? '';

        $extractedData = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Usa o método de parsing apropriado baseado no formato detectado
            $parsedLine = match ($pdfFormat) {
                self::PDF_FORMAT_2 => $this->parsePdfMovementLineFormat2($line, $accountFromFile),
                default => $this->parsePdfMovementLine($line, $accountFromFile),
            };

            if ($parsedLine !== null) {
                $extractedData[] = $parsedLine;
            }
        }

        return $extractedData;
    }

    /**
     * Extract text content from PDF file using Spatie PDF to Text
     *
     * Utiliza a opção -layout para preservar o formato tabular do extrato
     *
     * @throws GeneralExceptions
     */
    private function extractTextFromPdf(string $filePath): string
    {
        try {
            $text = Pdf::getText($filePath, null, ['layout']);

            if (empty($text)) {
                throw new GeneralExceptions(sprintf(self::ERROR_PDF_TEXT_EXTRACTION, $filePath), 500);
            }

            return $text;
        } catch (\Exception $e) {
            throw new GeneralExceptions(sprintf(self::ERROR_PDF_TEXT_EXTRACTION, $e->getMessage()), 500);
        }
    }

    /**
     * Parse a single movement line from PDF
     *
     * Formato esperado da linha (extraído com pdftotext -layout):
     * "01/07/2025   010856      PAG BOLETO                5.351,32 D"
     * "07/07/2025   091700      DP DIN ATM                1.404,00 C"
     */
    private function parsePdfMovementLine(string $line, string $account): ?ExtractorFileData
    {
        // Ignora linhas de SALDO DIA (documento 000000)
        if (Str::contains($line, self::PDF_DAILY_BALANCE_IDENTIFIER)) {
            return null;
        }

        // Ignora linhas que contêm apenas "Saldo" (linha de saldo após cada movimentação)
        if (preg_match('/^\s*Saldo\s+[\d.,]+\s+[CD]\s*$/', $line)) {
            return null;
        }

        // Regex para capturar linha de movimentação
        // Formato: DATA       NR.DOC      HISTÓRICO              VALOR   TIPO
        // Ex: "01/07/2025   010856      PAG BOLETO                5.351,32 D"
        $pattern = '/^(\d{2}\/\d{2}\/\d{4})\s+(\d{6})\s+(.+?)\s+([\d.,]+)\s+([CD])\s*$/';

        if (! preg_match($pattern, $line, $matches)) {
            return null;
        }

        $date = $matches[1];
        $documentNumber = $matches[2];
        $description = trim($matches[3]);
        $amount = $matches[4];
        $type = $matches[5];

        // Converte a data de DD/MM/YYYY para Y-m-d
        $movementDate = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');

        // Converte o valor de formato brasileiro (1.234,56) para float
        $amountFloat = $this->parseAmountFromPdf($amount);

        return new ExtractorFileData([
            'movementDate' => $movementDate,
            'description' => $description,
            'amount' => $amountFloat,
            'type' => $type,
            'documentNumber' => $documentNumber,
            'account' => $account,
        ]);
    }

    /**
     * Detect PDF format based on header structure
     *
     * Formato 1: Cabeçalho sem coluna "Saldo" - saldo aparece em linha separada
     * Formato 2: Cabeçalho com coluna "Saldo" - saldo na mesma linha da movimentação
     */
    private function detectPdfFormat(string $text): string
    {
        // Se o cabeçalho contém "Valor" seguido de "Saldo", é o Formato 2
        if (preg_match('/Valor\s+Saldo/i', $text)) {
            return self::PDF_FORMAT_2;
        }

        return self::PDF_FORMAT_1;
    }

    /**
     * Parse a single movement line from PDF Format 2
     *
     * Formato esperado da linha (extraído com pdftotext -layout):
     * "01/08/2025                 010708                          C PIX QRES           70,00 C    7.314,32 C"
     *
     * Neste formato, o saldo aparece na mesma linha após o tipo da movimentação
     */
    private function parsePdfMovementLineFormat2(string $line, string $account): ?ExtractorFileData
    {
        // Ignora linhas de SALDO DIA (documento 000000)
        if (Str::contains($line, self::PDF_DAILY_BALANCE_IDENTIFIER)) {
            return null;
        }

        // Ignora linha de SALDO ANTERIOR
        if (Str::contains($line, 'SALDO ANTERIOR')) {
            return null;
        }

        // Regex para capturar linha de movimentação do Formato 2
        // Formato: DATA   NR.DOC   HISTÓRICO   VALOR   TIPO   SALDO   TIPO_SALDO
        // Ex: "01/08/2025                 010708                          C PIX QRES           70,00 C    7.314,32 C"
        $pattern = '/^(\d{2}\/\d{2}\/\d{4})\s+(\d{6})\s+(.+?)\s+([\d.,]+)\s+([CD])\s+[\d.,]+\s+[CD]\s*$/';

        if (! preg_match($pattern, $line, $matches)) {
            return null;
        }

        $date = $matches[1];
        $documentNumber = $matches[2];
        $description = trim($matches[3]);
        $amount = $matches[4];
        $type = $matches[5];

        // Converte a data de DD/MM/YYYY para Y-m-d
        $movementDate = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');

        // Converte o valor de formato brasileiro (1.234,56) para float
        $amountFloat = $this->parseAmountFromPdf($amount);

        return new ExtractorFileData([
            'movementDate' => $movementDate,
            'description' => $description,
            'amount' => $amountFloat,
            'type' => $type,
            'documentNumber' => $documentNumber,
            'account' => $account,
        ]);
    }

    /**
     * Parse amount from PDF format to float
     *
     * Converte "1.604,35" para 1604.35
     */
    private function parseAmountFromPdf(string $amount): float
    {
        // Remove separador de milhar (.) e substitui vírgula decimal por ponto
        $normalized = str_replace('.', '', $amount);
        $normalized = str_replace(',', '.', $normalized);

        return (float) $normalized;
    }

    /**
     * Extract data from TXT format
     *
     * @throws GeneralExceptions
     */
    private function extractFromTxt(string $filePath, AccountFileData $file): array
    {
        $this->validateAccountFile($filePath, $file, 'TXT');
        $this->validateMonthFile($filePath, $file, 'TXT');

        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new GeneralExceptions("Unable to open file: {$filePath}", 500);
        }

        fgetcsv($handle, 0, ';');
        $rows = [];

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            // Validar o formato da linha antes de adicionar ao array
            if (! $this->validateTxtRowFormat($row)) {
                continue;
            }

            $rows[] = $row;
        }

        fclose($handle);

        return collect($rows)->map(fn ($row) => ExtractorFileData::fromFile($row))->toArray();
    }

    /**
     * Extract data from OFX format
     *
     * @throws GeneralExceptions
     */
    private function extractFromOfx(string $filePath, AccountFileData $file): array
    {
        $this->validateAccountFile($filePath, $file, 'OFX');
        $this->validateMonthFile($filePath, $file, 'OFX');

        // TODO: Implement OFX extraction logic for Caixa

        $extractedData = [];

        return $extractedData;
    }

    /**
     * Extract data from CSV format
     *
     * @throws GeneralExceptions
     */
    private function extractFromCsv(string $filePath, AccountFileData $file): array
    {
        $this->validateAccountFile($filePath, $file, 'CSV');
        $this->validateMonthFile($filePath, $file, 'CSV');

        // TODO: Implement CSV extraction logic for Caixa

        $extractedData = [];

        return $extractedData;
    }

    /**
     * Validate TXT row format
     *
     * Validates if the row has the correct format for Caixa TXT files:
     * - Must have exactly 6 fields
     * - Must not contain line breaks (\f, \n, \r)
     * - Essential fields must not be empty (account, date, description)
     */
    private function validateTxtRowFormat(array $row): bool
    {
        // Pular linhas vazias ou com apenas um elemento vazio
        if (empty($row) || (count($row) === 1 && trim($row[0]) === '')) {
            return false;
        }

        // Validar se a linha tem EXATAMENTE 6 campos (formato correto do extrato Caixa)
        // Formato esperado: [account, date, documentNumber, description, amount, type]
        if (count($row) !== 6) {
            return false;
        }

        // Validar se algum campo contém quebras de linha indevidas (\f, \n, \r)
        foreach ($row as $field) {
            if (str_contains($field, "\f") || str_contains($field, "\n") || str_contains($field, "\r")) {
                return false;
            }
        }

        // Validar se os campos essenciais não estão vazios (account, date, description)
        if (empty(trim($row[0], '"')) || empty(trim($row[1], '"')) || empty(trim($row[3], '"'))) {
            return false;
        }

        return true;
    }

    /**
     * Check if this extractor supports the given bank
     */
    public function supports(string $bankName): bool
    {
        return strtolower($bankName) === 'caixa' ||
               strtolower($bankName) === 'caixa economica federal';
    }
}
