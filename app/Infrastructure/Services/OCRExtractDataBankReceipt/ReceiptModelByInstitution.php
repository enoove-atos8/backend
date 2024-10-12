<?php

namespace Infrastructure\Services\OCRExtractDataBankReceipt;

class ReceiptModelByInstitution
{
    private array $response = [
        'status'    =>  '',
        'msg'       =>  '',
        'data'  =>  [
            'name'                  =>  '',
            'amount'                =>  0,
            'date'                  =>  '',
            'cpf'                   =>  '',
            'middle_cpf'            =>  '',
            'cnpj'                  =>  '',
            'institution'           =>  '',
            'timestamp_value_cpf'   =>  '',
        ]
    ];

    const CRON_LOG_PATH = 'logs/cron.log';



    /**
     * Function redirects to another
     * that will read the receipts according to the institution informed
     */
    public function handleDispatchDataFunctionByInstitution(array $dataExtracted, string | null $entryType, string $depositDate = ''): array | bool
    {
        $this->resetResponseArray();

        if($dataExtracted['templateReceipt'] == 'generic' && $dataExtracted['templateReceipt'] != '')
        {
            return $this->extractDataGenericReceipt($dataExtracted['text'], $entryType);
        }
        else if($dataExtracted['templateReceipt'] != '' && $dataExtracted['templateReceipt'] == 'receiptDeposit')
        {
            $text = $dataExtracted['text'];
            $amount = $dataExtracted['amount'];

            return $this->extractDataBankDeposit($text, $amount, $depositDate);
        }
        else if($dataExtracted['templateReceipt'] != 'generic' && $dataExtracted['templateReceipt'] != '')
        {
            $templateReceipt = $dataExtracted['templateReceipt'];
            $text = $dataExtracted['text'];

            return match ($templateReceipt)
            {
                'bb' => $this->extractDataBancoDoBrasil($text, $entryType),
                'cef_app' => $this->extractDataCaixaEconomicaAPP($text, $entryType),
                'cef_ger' => $this->extractDataGerenciadorCaixa($text, $entryType),
                'cef_ib' => $this->extractDataCaixaEconomicaIB($text, $entryType),
                'cef_tem' => $this->extractDataCaixaTEM($text, $entryType),
                'bradesco' => $this->extractDataBradesco($text, $entryType),
                'santander' => $this->extractDataSantander($text, $entryType),
                'itau_1' => $this->extractDataItau1($text, $entryType),
                'itau_2' => $this->extractDataItau2($text, $entryType),
                'sicredi' => $this->extractDataSicredi($text, $entryType),
                'nubank' => $this->extractDataNubank($text, $entryType),
                'digio' => $this->extractDataDigio($text, $entryType),
                'c6' => $this->extractDataC6($text, $entryType),
                'neon' => $this->extractDataNeon($text, $entryType),
                'inter' => $this->extractDataInter($text, $entryType),
                '99' => $this->extractData99($text, $entryType),
                'uber' => $this->extractDataUber($text, $entryType),
                'picpay' => $this->extractDataPicpay($text, $entryType),
                'iti' => $this->extractDataIti($text, $entryType),
                'next' => $this->extractDataNext($text, $entryType),
                'efi' => $this->extractDataEFI($text, $entryType),

                default => [
                    'status'    =>  'NOT_IMPLEMENTED',
                    'msg'       =>  '',
                    'data'  =>  [
                        'institution'  =>  'NOT_MAPPED',
                    ]
                ],
            };
        }
    }

    /**
     * Example of data extraction method for Banco do Brasil.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataBancoDoBrasil(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'BB';
        return $this->response;
    }


    /**
     * Example of data extraction method for CEF
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataCaixaEconomicaAPP(string $text, string $entryType): array
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING CEF DATA' . PHP_EOL);
        echo('=========================================' . PHP_EOL);

        //Get amount
        if ((preg_match('/RS\s*([\d,.]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);

        //Get name
        if ((preg_match('/Dados do pagador\s+Nome\s+([^\n]+)/', $text, $match)))
            $this->response['data']['name'] = $match[1];

        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[1];

        if($entryType == 'tithe')
        {
            //Get CPF
            if (preg_match('/CPF\s*\n(?:ees\s*)([0-9\s\.]+)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/[\s\.]/', '', $match[1]);
            else if(preg_match('/CPF\s*(.*)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
            else if(preg_match('/GPF\s*(.*)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        }

        if($entryType == 'tithe')
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        }
        else
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
        }


        if($entryType == 'tithe')
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'CEF';
        }
        else
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'CEF';
        }


        return $this->response;
    }


    /**
     * Example of data extraction method for GER_CEF
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataGerenciadorCaixa(string $text, string $entryType): array
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING GER_CEF DATA' . PHP_EOL);
        echo('=========================================' . PHP_EOL);


        //Get amount
        if (preg_match('/R\$\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/', $text, $match))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[0]);


        //Get name
        if (preg_match('/Origem\s+Nome:\s*(.+)$/', $text, $match))
            $this->response['data']['name'] = $match[1];


        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4}) as/', $text, $match))
            $this->response['data']['date'] = $match[1];

        if($entryType == 'tithe')
        {
            //Get CPF
            if (preg_match('/CPF:\s?(\d{11})/', $text, $match))
                $this->response['data']['cpf'] = $match[1];
        }


        if($entryType == 'tithe')
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['cpf'];
        }
        else
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
        }


        if($entryType == 'tithe')
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'GER_CEF';
        }
        else
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'GER_CEF';
        }

        return $this->response;
    }


    /**
     * Example of data extraction method for CEF_IB
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataCaixaEconomicaIB(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'CEF_IB';
        return $this->response;
    }


    /**
     * Example of data extraction method for CAIXA_TEM
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataCaixaTEM(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'CAIXA_TEM';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataBradesco(string $text, string $entryType): array
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING BRADESCO DATA' . PHP_EOL);
        echo('=========================================' . PHP_EOL);

        //Get amount
        if (preg_match('/R\$\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/', $text, $match))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[0]);


        //Get name
        if (preg_match('/Dados de quem pagou\s+Nome:\s*(.+)$/', $text, $match))
            $this->response['data']['name'] = $match[1];


        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[0];

        if($entryType == 'tithe')
        {
            //Get CPF
            if (preg_match('/CPF:\s*([\*\.0-9\-]+)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        }


        if($entryType == 'tithe')
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        }
        else
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
        }


        if($entryType == 'tithe')
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'BRADESCO';
        }
        else
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'BRADESCO';
        }


        return $this->response;
    }


    /**
     * Example of data extraction method for Sandander.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataSantander(string $text, string $entryType): array
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING SANTANDER DATA' . PHP_EOL);
        echo('=========================================' . PHP_EOL);

        //Get amount
        if (preg_match('/R\$\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/', $text, $match))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[0]);


        //Get name
        if (preg_match('/^De:\s*(.+)$/', $text, $match))
            $this->response['data']['name'] = $match[1];

        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[1];

        if($entryType == 'tithe')
        {
            //Get CPF
            if (preg_match('/CPF\s*([\*\,\.\d\-]+)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        }


        if($entryType == 'tithe')
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        }
        else
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
        }


        if($entryType == 'tithe')
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'SANTANDER';
        }
        else
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'SANTANDER';
        }


        return $this->response;
    }


    /**
     * Example of data extraction method for Itau_1.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataItau1(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'ITAU1';
        return $this->response;
    }


    /**
     * Example of data extraction method for Itau_2.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataItau2(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'ITAU2';
        return $this->response;
    }


    /**
     * Example of data extraction method for Sicredi.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataSicredi(string $text, string $entryType): array
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING SICREDI DATA' . PHP_EOL);
        echo('=========================================' . PHP_EOL);

        //Get amount
        if (preg_match('/RS\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/', $text, $match))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[0]);


        //Get name
        if (preg_match('/^Solicitante:\s*(.+)$/', $text, $match))
            $this->response['data']['name'] = $match[1];

        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[0];

        if($entryType == 'tithe')
        {
            //Get CPF
            if (preg_match('/CPF\s+do\s+pagador:\s+([\d\*\.]+)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        }


        if($entryType == 'tithe')
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        }
        else
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
        }


        if($entryType == 'tithe')
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'SICREDI';
        }
        else
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'SICREDI';
        }

        return $this->response;
    }


    /**
     * Example of data extraction method for Nubank.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataNubank(string $text, string $entryType): array
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING NUBANK DATA' . PHP_EOL);
        echo('=========================================' . PHP_EOL);


        //Get amount
        if ((preg_match('/R\$.(\d.*)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);

        //Get name
        if ((preg_match('/Origem\s+Nome\s+([^\n]+)/', $text, $match)))
            $this->response['data']['name'] = $match[1];


        //Get date
        if (preg_match('/\d{2} [A-Z]{3} \d{4}\b/', $text, $match))
            $this->response['data']['date'] = $this->formatDateWithTextMonth($match[0]);

        if($entryType == 'tithe')
        {
            //Get CPF
            if (preg_match('/CPF.*?(\d{3})[,.]*\d?[,.]*([\d][ ]?\d[ ]?\d)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/[\s\.]/', '', $match[1]) . preg_replace('/[\s\.]/', '', $match[2]);
            else if(preg_match('/.*?(\d{3})[,](\d{3})/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]);
        }

        if($entryType == 'tithe')
        {
            //Get timestamp
            if (preg_match('/(\d{2} \w{3} \d{4}) - (\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        }
        else
        {
            //Get timestamp
            if (preg_match('/(\d{2} \w{3} \d{4}) - (\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
        }


        if($entryType == 'tithe')
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'NUBANK';
        }
        else
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'NUBANK';
        }


        return $this->response;
    }


    /**
     * Example of data extraction method for Digio.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataDigio(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'DIGIO';
        return $this->response;
    }


    /**
     * Example of data extraction method for C6.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataC6(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'C6';
        return $this->response;
    }


    /**
     * Example of data extraction method for NEON.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataNeon(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'NEON';
        return $this->response;
    }


    /**
     * Example of data extraction method for Inter.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataInter(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'INTER';
        return $this->response;
    }


    /**
     * Example of data extraction method for 99.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractData99(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = '99';
        return $this->response;
    }


    /**
     * Example of data extraction method for Uber.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataUber(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'UBER';
        return $this->response;
    }


    /**
     * Example of data extraction method for Picpay.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataPicpay(string $text, string $entryType): array
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING PICPAY DATA ' . PHP_EOL);
        echo('========================================= ' . PHP_EOL);

        //Get amount
        if ((preg_match('/R\$\s*([\d,.]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);
        if ((preg_match('/RS\s*([\d,.]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);


        //Get name
        if ((preg_match('/De\s+([A-Z\s]+)/', $text, $match)))
            $this->response['data']['name'] =  str_replace("\n", ' ', $match[1]);

        //Get date
        if (preg_match('/\d{2}\/[a-z]{3}\/\d{4}/', $text, $match))
            $this->response['data']['date'] = $this->formatDateWithTextMonth($match[0]);

        if($entryType == 'tithe')
        {
            //Get CPF
            if (preg_match('/\*\*\*\s*([\d.]+)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/[\s\.]/', '', $match[1]);
            else if(preg_match('/CPF\s*(.*)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
            else if(preg_match('/GPF\s*(.*)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
            else if(preg_match('/(\d{3})[,](\d{3})/', $text, $match))
                $this->response['data']['middle_cpf'] = $match[1] . $match[2];
        }


        if($entryType == 'tithe')
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/[a-z]{3}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        }
        else
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/[a-z]{3}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
        }


        if($entryType == 'tithe')
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'PICPAY';
        }
        else
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'PICPAY';
        }


        return $this->response;
    }


    /**
     * Example of data extraction method for ITI.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataIti(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'ITI';
        return $this->response;
    }



    /**
     * Example of data extraction method for ITI.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataEFI(string $text, string $entryType): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'EFI';
        return $this->response;
    }


    /**
     * Example of data extraction method for NEXT.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataNext(string $text, string $entryType): array
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING NEXT DATA' . PHP_EOL);
        echo('=========================================' . PHP_EOL);

        //Get amount
        if ((preg_match('/Valor:\s*R\$ ([\d.,]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);


        //Get name
        if ((preg_match('/Dados do pagador\s+Nome:\s*(.+)$/', $text, $match)))
            $this->response['data']['name'] = $match[1];

        //Get date
        if (preg_match('/Data:\s*(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[1];

        if($entryType == 'tithe')
        {
            //Get CPF
            if (preg_match('/CPF:.*?(\d[ .]?\d{2})\.(\d{3})/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]);
        }


        if($entryType == 'tithe')
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        }
        else
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
        }


        if($entryType == 'tithe')
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'NEXT';
        }
        else
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'NEXT';
        }


        return $this->response;
    }


    /**
     * Example of data extraction method for Banco do Generic.
     *
     * @param string $text
     * @param string $entryType
     * @return array
     */
    private function extractDataGenericReceipt(string $text, string $entryType): array
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING UNIDENTIFIED INSTITUTION DATA' . PHP_EOL);
        echo('=========================================' . PHP_EOL);

        //Get amount
        if ((preg_match('/RS\s*([\d,.]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);

        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[1];

        if($entryType == 'tithe')
        {
            //Get CPF
            if (preg_match('/CPF\s*\n(?:ees\s*)([0-9\s\.]+)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/[\s\.]/', '', $match[1]);
            else if(preg_match('/CPF\s*(.*)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
            else if(preg_match('/GPF\s*(.*)/', $text, $match))
                $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        }


        if($entryType == 'tithe')
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/[a-z]{3}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
            if (preg_match('/(\d{2} \w{3} \d{4}) - (\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        }
        else
        {
            //Get timestamp
            if (preg_match('/(\d{2}\/[a-z]{3}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
            if (preg_match('/(\d{2} \w{3} \d{4}) - (\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
                $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'];
        }


        if($entryType == 'tithe')
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'UNIDENTIFIED_INSTITUTION';
        }
        else
        {
            if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
                $this->response['status'] = 'READING_ERROR';
            else
                $this->response['status'] = 'SUCCESS';

            $this->response['data']['institution'] = 'UNIDENTIFIED_INSTITUTION';
        }

        return $this->response;
    }




    /**
     * Example of data extraction method for Banco do Generic.
     *
     * @param string $text
     * @param string $amount
     * @param string $depositDate
     * @return bool
     */
    private function extractDataBankDeposit(string $text, string $amount, string $depositDate): bool
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo('EXTRACTING RECEIPT BANK DATA' . PHP_EOL);
        echo('=========================================' . PHP_EOL);

        $amountFounded = false;
        $dateFounded = false;
        $amountValue = null;
        $dateValue = null;

        if((preg_match('/RS\s*([\d,.]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $amountValue = $match[1];

        if (str_contains($amount, $amountValue))
            $amountFounded = true;


        if(preg_match('/(\d{2}\/\d{2}\/\d{4})\sas\s(\d{2}:\d{2}:\d{2})/', $text, $match))
            $dateValue = $match[1];

        if (str_contains($depositDate, $dateValue))
            $dateFounded = true;


        if($amountFounded)
            return true;
        else
            return false;
    }

    /**
     * Transform 00/abc/0000 format date in 00/00/0000
     */
    public function formatDateWithTextMonth(string $date): string
    {
        $arrMonthTextToNumber = [
            'jan' => '01', 'fev' => '02', 'mar' => '03', 'abr' => '04',
            'mai' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08',
            'set' => '09', 'out' => '10', 'nov' => '11', 'dez' => '12'
        ];

        if(count(explode('/', $date)) == 3)
            $arrDateParts = explode('/', $date);
        else if(count(explode(' ', $date)) == 3)
            $arrDateParts = explode(' ', $date);

        $day = $arrDateParts[0];
        $monthText = strtolower($arrDateParts[1]);
        $monthNumber = $arrMonthTextToNumber[$monthText];
        $year = $arrDateParts[2];

        return $day . '/' . $monthNumber . '/' . $year;
    }



    /**
     * Reset response data
     */
    private function resetResponseArray(): void
    {
        $this->response['status'] = '';
        $this->response['msg'] = '';
        $this->response['data']['name'] = '';
        $this->response['data']['amount'] = 0;
        $this->response['data']['date'] = '';
        $this->response['data']['cpf'] = '';
        $this->response['data']['middle_cpf'] = '';
        $this->response['data']['cnpj'] = '';
        $this->response['data']['institution'] = '';
        $this->response['data']['timestamp_value_cpf'] = '';

    }
}
