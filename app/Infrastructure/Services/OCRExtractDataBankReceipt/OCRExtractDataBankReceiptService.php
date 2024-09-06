<?php

namespace Infrastructure\Services\OCRExtractDataBankReceipt;

use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class OCRExtractDataBankReceiptService
{
    const SEARCH_TERMS_IN_RECEIPT_BY_INSTITUTIONS = [
        'bb'        => 'SISBB',
        'cef_app'   => 'SAC CAIXA',
        'cef_ger'   => 'Via Gerenciador CAIXA',
        'cef_ib'    => 'Via Internet Banking CAIXA',
        'cef_tem'   => 'NSU',
        'bradesco'  => 'BRADESCO CELULAR',
        'santander' => 'Central de Atendimento Santander',
        'itau_1'    => 'App Itaú',
        'itau_2'    => 'via app itaú',
        'itau_3'    => 'via app Itaú',
        'sicredi'   => 'Cooperativa e conta de origem',
        'nubank'    => 'Nu Pagamentos S.A.',
        'digio'     => '',
        'c6'        => '31872495 - Banco C6 S.A.',
        'neon'      => 'NEON PAGAMENTOS',
        'inter'     => 'Banco Inter S.A.',
        '99'        => '',
        'uber'      => '',
        'picpay'    => 'PicPay Instituição de Pagamentos S.A',
        'iti'       => '',
    ];

    /**
     * @param $file
     * @return string|bool
     * @throws TesseractOcrException
     */
    public function getBankingInstitution($file): string | bool
    {
        $readText = (new TesseractOCR($file))->run();

        foreach (self::SEARCH_TERMS_IN_RECEIPT_BY_INSTITUTIONS as $template => $term)
        {
            if (str_contains($readText, $term))
            {
                $this->handleExtractDataByInstitution($template, $readText);
                return $template;
            }
        }

        return false;
    }

    /**
     * Método principal para a extração de dados do recibo bancário.
     *
     * @param string $filePath
     * @return array|bool
     * @throws TesseractOcrException
     */
    public function ocrExtractData(string $filePath): array | bool
    {
        $institution = $this->getBankingInstitution($filePath);

        if($institution !== false)
            return $this->handleExtractDataByInstitution($institution, $filePath);
        else
            return false;
    }

    /**
     * Extrai dados com base na instituição bancária identificada.
     *
     * @param string $template
     * @param string $text
     * @return array
     */
    public function handleExtractDataByInstitution(string $template, string $text): array
    {
        $extractedData = [];

        switch ($template) {
            case 'bb':
                $extractedData = $this->extractDataBancoDoBrasil($text);
                break;
            case 'cef_app':
            case 'cef_ger':
            case 'cef_ib':
            case 'cef_tem':
                $extractedData = $this->extractDataCaixaEconomica($text);
                break;
            case 'bradesco':
                $extractedData = $this->extractDataBradesco($text);
                break;

            // Outras instituições podem ser adicionadas aqui

            default:
                echo "Banco não identificado ou não suportado.";
                break;
        }

        return $extractedData;
    }

    /**
     * Exemplo de método de extração de dados para o Banco do Brasil.
     *
     * @param string $text
     * @return array
     */
    private function extractDataBancoDoBrasil(string $text): array
    {
        return [
            'instituicao' => 'Banco do Brasil',
            'dados' => 'Exemplo de dados extraídos do Banco do Brasil'
        ];
    }

    /**
     * Exemplo de método de extração de dados para a Caixa Econômica Federal.
     *
     * @param string $text
     * @return array
     */
    private function extractDataCaixaEconomica(string $text): array
    {
        return [
            'instituicao' => 'Caixa Econômica Federal',
            'dados' => 'Exemplo de dados extraídos da Caixa Econômica Federal'
        ];
    }


    /**
     * Exemplo de método de extração de dados para o Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataBradesco(string $text): array
    {
        return [
            'instituicao' => 'Bradesco',
            'dados' => 'Exemplo de dados extraídos do Bradesco'
        ];
    }
}
