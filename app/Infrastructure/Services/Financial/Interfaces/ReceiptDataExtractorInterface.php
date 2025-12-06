<?php

namespace App\Infrastructure\Services\Financial\Interfaces;

interface ReceiptDataExtractorInterface
{
    /**
     * Extrai dados de um comprovante bancário
     *
     * @param  array{destinationPath: string, fullFileNamePdf: ?string, fileUploaded: string}  $arrFilePath
     * @return array{status: string, msg: string, data: array<string, mixed>}
     */
    public function extractData(array $arrFilePath, string $docType, string $docSubType): array;
}
