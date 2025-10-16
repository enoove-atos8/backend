<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\Extractors;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\DataTransferObjects\ExtractorFileData;
use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\Interfaces\BankStatementExtractorInterface;
use Illuminate\Support\Str;
use Infrastructure\Exceptions\GeneralExceptions;

class CaixaStatementExtractor implements BankStatementExtractorInterface
{
    /**
     * Extract bank statement data from Caixa file
     *
     * @param string $filePath * @return array
     * @param AccountFileData $file
     * @return array
     * @throws GeneralExceptions
     */
    public function extract(string $filePath, AccountFileData $file): array
    {
        return match(strtoupper($file->fileType)) {
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
     * @param string $filePath
     * @param AccountFileData $file
     * @param string $fileType
     * @return void
     * @throws GeneralExceptions
     */
    private function validateAccountFile(string $filePath, AccountFileData $file, string $fileType): void
    {
        $accountNumber = str_replace('-', '', $file->account->accountNumber);

        match(strtoupper($fileType)) {
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
     * @param string $filePath
     * @param string $accountNumber
     * @return void
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
            throw new GeneralExceptions("File is empty or has invalid format", 422);
        }

        $accountInFile = trim($firstRow[0], '"');

        if (!Str::contains($accountInFile, $accountNumber)) {
            throw new GeneralExceptions(
                "Account validation failed: Expected account '{$accountNumber}' not found in file data '{$accountInFile}'",
                422
            );
        }
    }

    /**
     * Validate if the file movements match the expected reference month
     *
     * @param string $filePath
     * @param AccountFileData $file
     * @param string $fileType
     * @return void
     * @throws GeneralExceptions
     */
    private function validateMonthFile(string $filePath, AccountFileData $file, string $fileType): void
    {
        $referenceMonth = str_replace('-', '', $file->referenceDate);

        match(strtoupper($fileType)) {
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
     * @param string $filePath
     * @param string $referenceMonth
     * @return void
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
            throw new GeneralExceptions("File is empty or has invalid format", 423);
        }

        $movementDateInFile = trim($firstRow[1], '"');

        if (!Str::contains($movementDateInFile, $referenceMonth)) {
            throw new GeneralExceptions(
                "Month validation failed: Expected month '{$referenceMonth}' not found in file movement date '{$movementDateInFile}'",
                423
            );
        }
    }

    /**
     * Validate PDF file month
     *
     * @param string $filePath
     * @param string $referenceMonth
     * @return void
     */
    private function validatePdfMonth(string $filePath, string $referenceMonth): void
    {
        // TODO: Implement PDF month validation logic for Caixa
        // Extract movement dates from PDF and validate
    }

    /**
     * Validate CSV file month
     *
     * @param string $filePath
     * @param string $referenceMonth
     * @return void
     */
    private function validateCsvMonth(string $filePath, string $referenceMonth): void
    {
        // TODO: Implement CSV month validation logic for Caixa
        // Extract movement dates from CSV and validate
    }

    /**
     * Validate OFX file month
     *
     * @param string $filePath
     * @param string $referenceMonth
     * @return void
     */
    private function validateOfxMonth(string $filePath, string $referenceMonth): void
    {
        // TODO: Implement OFX month validation logic for Caixa
        // Extract movement dates from OFX and validate
    }

    /**
     * Validate PDF file account
     *
     * @param string $filePath
     * @param string $accountNumber
     * @return void
     */
    private function validatePdfAccount(string $filePath, string $accountNumber): void
    {
        // TODO: Implement PDF account validation logic for Caixa
        // Extract account information from PDF and validate
    }

    /**
     * Validate CSV file account
     *
     * @param string $filePath
     * @param string $accountNumber
     * @return void
     * @throws GeneralExceptions
     */
    private function validateCsvAccount(string $filePath, string $accountNumber): void
    {
        // TODO: Implement CSV account validation logic for Caixa
        // Extract account information from CSV and validate
    }

    /**
     * Validate OFX file account
     *
     * @param string $filePath
     * @param string $accountNumber
     * @return void
     * @throws GeneralExceptions
     */
    private function validateOfxAccount(string $filePath, string $accountNumber): void
    {
        // TODO: Implement OFX account validation logic for Caixa
        // Extract account information from OFX and validate
    }

    /**
     * Extract data from PDF format
     *
     * @param string $filePath
     * @param \App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData $file
     * @return array
     * @throws GeneralExceptions
     */
    private function extractFromPdf(string $filePath, AccountFileData $file): array
    {
        $this->validateAccountFile($filePath, $file, 'PDF');
        $this->validateMonthFile($filePath, $file, 'PDF');

        // TODO: Implement PDF extraction logic for Caixa

        $extractedData = [];

        return $extractedData;
    }

    /**
     * Extract data from TXT format
     *
     * @param string $filePath
     * @param \App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData $file
     * @return array
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
            $rows[] = $row;
        }

        fclose($handle);

        return collect($rows)->map(fn($row) => ExtractorFileData::fromFile($row))->toArray();
    }

    /**
     * Extract data from OFX format
     *
     * @param string $filePath
     * @param \App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData $file
     * @return array
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
     * @param string $filePath
     * @param \App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData $file
     * @return array
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
     * Check if this extractor supports the given bank
     *
     * @param string $bankName
     * @return bool
     */
    public function supports(string $bankName): bool
    {
        return strtolower($bankName) === 'caixa' ||
               strtolower($bankName) === 'caixa economica federal';
    }
}
