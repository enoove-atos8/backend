<?php

namespace App\Infrastructure\Services\Atos8\Financial\Entries\Automation\OCRExtractDataBankReceipt;

use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class OCRExtractDataBankReceiptService
{
    private ReceiptModelByInstitution $receiptModelByInstitution;
    const SEARCH_TERMS_IN_RECEIPT_BY_INSTITUTIONS = [
        'bb'        => 'SISBB',
        'cef_app'   => 'CAIXA',
        'cef_ger'   => 'Gerenciador CAIXA',
        'cef_ib'    => 'Internet Banking CAIXA',
        'cef_tem'   => 'NSU',
        'bradesco'  => 'BRADESCO',
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
        'inter'     => 'INTER',
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


    /**
     * Main method for extracting data from bank receipt.
     *
     * @param string $filePath
     * @param string|null $entryType
     * @param string $totalAmount
     * @param string $depositDate
     * @return array|string|bool
     * @throws TesseractOcrException
     */
    public function ocrExtractData(string $filePath, string | null $entryType, string $totalAmount = '', string $depositDate = ''): array | string | bool
    {
        $dataExtracted = $this->getBankingInstitution($filePath, $totalAmount);
        return $this->receiptModelByInstitution->handleDispatchDataFunctionByInstitution($dataExtracted, $entryType, $depositDate);
    }


    /**
     * @param string $file
     * @param string $totalAmount
     * @return string|bool
     * @throws TesseractOcrException
     */
    public function getBankingInstitution(string $file, string $totalAmount = ''): array | bool
    {
        $ocr = new TesseractOCR($file);
        $readText = $ocr->run();

        if($totalAmount == '')
        {
            foreach (self::SEARCH_TERMS_IN_RECEIPT_BY_INSTITUTIONS as $templateReceipt => $term)
            {
                if($templateReceipt == 'cef_app')
                {
                    $regex1 = '/Dados do pagador.*?Instituic¢Go.*?([A-Z\w]+)/s';
                    $regex2 = '/Dados do pagador.*?InstituigGo.*?([A-Z\w]+)/s';
                    $regex3 = '/Dados do pagador.*?Instituicdo.*?([A-Z\w]+)/s';
                    $regex4 = '/Dados do pagador.*?Institui¢Go.*?([A-Z\w]+)/s';
                    $regex5 = '/Dados do pagador.*?InstituicGo.*?([A-Z\w]+)/s';

                    if((preg_match($regex1, $readText, $matches)) ||
                        (preg_match($regex2, $readText, $matches)) ||
                        (preg_match($regex3, $readText, $matches)) ||
                        (preg_match($regex4, $readText, $matches)) ||
                        (preg_match($regex5, $readText, $matches)))
                    {
                        if($matches[1] == $term)
                        {
                            return [
                                'templateReceipt'   =>  $templateReceipt,
                                'text'              =>  $readText
                            ];
                        }
                    }
                }
                else if (str_contains($readText, $term))
                {
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
        else
        {
            return [
                'templateReceipt'   =>  'receiptDeposit',
                'text'              =>  $readText,
                'amount'            => $totalAmount
            ];
        }
    }


    /**
     * Convert PDF to Image
     */
    private function verifyFormatFile(string $file): string
    {
        $resolutionImage = 250;

        if(strpos($file, '.pdf'))
        {
            $fileNameToJpg = preg_replace('/\.pdf$/', '', $file);
            exec("pdftoppm -jpeg -f 1 -l 1 -rx $resolutionImage -ry $resolutionImage $file $fileNameToJpg");

            $fileNameWithNumberSufix = preg_replace('/\.jpg$/', '-1.jpg', $fileNameToJpg . '.jpg');

            rename($fileNameWithNumberSufix, $fileNameToJpg . '.jpg');

            return $fileNameToJpg . '.jpg';
        }
        else
        {
            return '';
        }
    }
}
