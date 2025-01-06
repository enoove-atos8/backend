<?php

namespace App\Infrastructure\Services\Atos8\Financial\Entries\Automation\OCRExtractDataBankReceipt;

use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRExtractDataBankReceiptService
{
    private ReceiptModelByInstitution $receiptModelByInstitution;
    const SEARCH_TERMS_IN_RECEIPT_BY_INSTITUTIONS = [
        'bb'        => 'SISBB',
        'cef_app'   => 'CAIXA',
        'cef_ger'   => 'Gerenciador CAIXA',
        'cef_ib'    => 'Internet Banking CAIXA',
        'cef_tem'   => 'NSU',
        'bradesco'  => ['Bradesco', 'BRADESCO'],
        'santander' => 'SANTANDER',
        'itau_1'    => 'ITAU UNIBANCO',
        'itau_2'    => '341 Ita',
        'itau_3'    => 'via app Itau',
        'itau_4'    => 'via app itau',
        'sicredi'   => 'SICREDI',
        'nubank'    => 'Nu Pagamentos',
        'digio'     => 'Digio',
        'c6'        => 'Banco C6',
        'neon'      => 'NEON PAGAMENTOS',
        'inter'     => ['INTER','Inter'],
        //'99'        => '',
        //'uber'      => '',
        'picpay'    => 'PICPAY',
        //'iti'       => '',
        'efi'       => 'EFI',
        'next'      => 'Next',
    ];

    public function __construct(ReceiptModelByInstitution $receiptModelByInstitution)
    {
        $this->receiptModelByInstitution = $receiptModelByInstitution;
    }

    public function ocrExtractData(array $arrFilePath, string | null $entryType): array | string | bool
    {
        $dataExtracted = $this->getBankingInstitution($arrFilePath);
        return $this->receiptModelByInstitution->handleDispatchDataFunctionByInstitution($dataExtracted, $entryType);
    }

    public function getBankingInstitution(array $arrFilePath): array | bool
    {
        $readText = $this->getTextFromFile($arrFilePath);

        foreach (self::SEARCH_TERMS_IN_RECEIPT_BY_INSTITUTIONS as $templateReceipt => $term)
        {
            if ($this->matchTemplateReceipt($templateReceipt, $term, $readText)) {
                return [
                    'templateReceipt'   =>  $templateReceipt,
                    'text'              =>  $readText
                ];
            }
        }

        return [
            'templateReceipt'   =>  'generic',
            'text'              =>  $readText
        ];
    }

    private function getTextFromFile(array $arrFilePath): ?string
    {
        if (!is_null($arrFilePath['fullFileNamePdf'])) {
            $pathToPdf = $arrFilePath['fullFileNamePdf'];
            $readText = Pdf::getText($pathToPdf);

            if (empty($readText)) {
                $ocr = new TesseractOCR($arrFilePath['destinationPath']);
                $readText = $ocr->run();
            }
        } else {
            $ocr = new TesseractOCR($arrFilePath['destinationPath']);
            $readText = $ocr->run();
        }

        return $readText;
    }

    private function matchTemplateReceipt(string $templateReceipt, $term, string $readText): bool
    {
        if (is_array($term)) {
            foreach ($term as $value) {
                if ($this->matchTerm($templateReceipt, $value, $readText)) {
                    return true;
                }
            }
        } else {
            return $this->matchTerm($templateReceipt, $term, $readText);
        }
        return false;
    }

    private function matchTerm(string $templateReceipt, string $term, string $readText): bool
    {
        if ($templateReceipt == 'cef_app') {
            $regexes = [
                '/Dados do pagador.*?Instituic¢Go.*?([A-Z\w]+)/s',
                '/Dados do pagador.*?InstituigGo.*?([A-Z\w]+)/s',
                '/Dados do pagador.*?Instituicdo.*?([A-Z\w]+)/s',
                '/Dados do pagador.*?Institui¢Go.*?([A-Z\w]+)/s',
                '/Dados do pagador.*?InstituicGo.*?([A-Z\w]+)/s',
            ];

            foreach ($regexes as $regex) {
                if (preg_match($regex, $readText, $matches) && $matches[1] == $term) {
                    return true;
                }
            }
        } else if (str_contains($readText, $term)) {
            return true;
        }
        return false;
    }
}
