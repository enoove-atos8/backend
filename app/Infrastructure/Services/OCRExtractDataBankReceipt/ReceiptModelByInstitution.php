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



    /**
     * Function redirects to another
     * that will read the receipts according to the institution informed
     */
    public function handleDispatchDataFunctionByInstitution($dataExtracted): array
    {
        if($dataExtracted['templateReceipt'] == 'generic' && $dataExtracted['templateReceipt'] != '')
        {
            return $this->extractDataGenericReceipt($dataExtracted['text']);
        }
        else if($dataExtracted['templateReceipt'] != 'generic' && $dataExtracted['templateReceipt'] != '')
        {
            $templateReceipt = $dataExtracted['templateReceipt'];
            $text = $dataExtracted['text'];

            return match ($templateReceipt)
            {
                'bb' => $this->extractDataBancoDoBrasil($text),
                'cef_app' => $this->extractDataCaixaEconomicaAPP($text),
                'cef_ger' => $this->extractDataGerenciadorCaixa($text),
                'cef_ib' => $this->extractDataCaixaEconomicaIB($text),
                'cef_tem' => $this->extractDataCaixaTEM($text),
                'bradesco' => $this->extractDataBradesco($text),
                'santander' => $this->extractDataSantander($text),
                'itau_1' => $this->extractDataItau1($text),
                'itau_2' => $this->extractDataItau2($text),
                'sicredi' => $this->extractDataSicredi($text),
                'nubank' => $this->extractDataNubank($text),
                'digio' => $this->extractDataDigio($text),
                'c6' => $this->extractDataC6($text),
                'neon' => $this->extractDataNeon($text),
                'inter' => $this->extractDataInter($text),
                '99' => $this->extractData99($text),
                'uber' => $this->extractDataUber($text),
                'picpay' => $this->extractDataPicpay($text),
                'iti' => $this->extractDataIti($text),
                'next' => $this->extractDataNext($text),

                default => [
                    'institution' => 'Instituição não identificada',
                ],
            };
        }
    }


    /**
     * Example of data extraction method for Banco do Brasil.
     *
     * @param string $text
     * @return array
     */
    private function extractDataBancoDoBrasil(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'BB';
        return $this->response;
    }


    /**
     * Example of data extraction method for Banco do Brasil.
     *
     * @param string $text
     * @return array
     */
    private function extractDataCaixaEconomicaAPP(string $text): array
    {
        printf('=========================================');
        printf('EXTRACTING CEF DATA');
        printf('=========================================');

        //Get name
        if ((preg_match('/Dados do pagador\s+Nome\s+([^\n]+)/', $text, $match)))
            $this->response['data']['name'] = $match[1];
        else {
            $this->response['data']['institution'] = 'CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET NAME DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }

        //Get amount
        if ((preg_match('/RS\s*([\d,.]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);
        else {
            $this->response['data']['institution'] = 'CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET AMOUNT DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }


        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[1];
        else
        {
            $this->response['data']['institution'] = 'CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET DATE DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get CPF
        if (preg_match('/CPF\s*\n(?:ees\s*)([0-9\s\.]+)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/[\s\.]/', '', $match[1]);
        else if(preg_match('/CPF\s*(.*)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        else if(preg_match('/GPF\s*(.*)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        else
        {
            $this->response['data']['institution'] = 'CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET CPF DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        //Get timestamp
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        else
        {
            $this->response['data']['institution'] = 'CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET TIMESTAMP DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
            $this->response['status'] = 'NOT_IMPLEMENTED';
        else
            $this->response['status'] = 'SUCCESS';

        $this->response['data']['institution'] = 'CEF';

        return $this->response;
    }


    /**
     * Example of data extraction method for Banco do Brasil.
     *
     * @param string $text
     * @return array
     */
    private function extractDataGerenciadorCaixa(string $text): array
    {
        printf('=========================================');
        printf('EXTRACTING GER_CEF DATA');
        printf('=========================================');

        //Get name
        if (preg_match('/Origem\s+Nome:\s*(.+)$/', $text, $match))
            $this->response['data']['name'] = $match[1];
        else {
            $this->response['data']['institution'] = 'GER_CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET NAME DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }

        //Get amount
        if (preg_match('/R\$\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/', $text, $match))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[0]);
        else {
            $this->response['data']['institution'] = 'GER_CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET AMOUNT DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }


        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4}) as/', $text, $match))
            $this->response['data']['date'] = $match[1];
        else
        {
            $this->response['data']['institution'] = 'GER_CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET DATE DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get CPF
        if (preg_match('/CPF:\s?(\d{11})/', $text, $match))
            $this->response['data']['cpf'] = $match[1];
        else
        {
            $this->response['data']['institution'] = 'GER_CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET CPF DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        //Get timestamp
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['cpf'];
        else
        {
            $this->response['data']['institution'] = 'GER_CEF';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET TIMESTAMP DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
            $this->response['status'] = 'NOT_IMPLEMENTED';
        else
            $this->response['status'] = 'SUCCESS';

        $this->response['data']['institution'] = 'GER_CEF';

        return $this->response;
    }


    /**
     * Example of data extraction method for Banco do Brasil.
     *
     * @param string $text
     * @return array
     */
    private function extractDataCaixaEconomicaIB(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'CEF_IB';
        return $this->response;
    }


    /**
     * Example of data extraction method for Banco do Brasil.
     *
     * @param string $text
     * @return array
     */
    private function extractDataCaixaTEM(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'CAIXA_TEM';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataBradesco(string $text): array
    {
        printf('=========================================');
        printf('EXTRACTING BRADESCO DATA');
        printf('=========================================');

        //Get name
        if (preg_match('/Dados de quem pagou\s+Nome:\s*(.+)$/', $text, $match))
            $this->response['data']['name'] = $match[1];
        else {
            $this->response['data']['institution'] = 'BRADESCO';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET NAME DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }

        //Get amount
        if (preg_match('/R\$\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/', $text, $match))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[0]);
        else {
            $this->response['data']['institution'] = 'BRADESCO';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET AMOUNT DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }


        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[0];
        else
        {
            $this->response['data']['institution'] = 'BRADESCO';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET DATE DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get CPF
        if (preg_match('/CPF:\s*([\*\.0-9\-]+)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        else
        {
            $this->response['data']['institution'] = 'BRADESCO';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET CPF DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        //Get timestamp
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        else
        {
            $this->response['data']['institution'] = 'BRADESCO';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET TIMESTAMP DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
            $this->response['status'] = 'NOT_IMPLEMENTED';
        else
            $this->response['status'] = 'SUCCESS';

        $this->response['data']['institution'] = 'BRADESCO';

        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataSantander(string $text): array
    {
        printf('=========================================');
        printf('EXTRACTING SANTANDER DATA');
        printf('=========================================');

        //Get name
        if (preg_match('/^De:\s*(.+)$/', $text, $match))
            $this->response['data']['name'] = $match[1];
        else {
            $this->response['data']['institution'] = 'SANTANDER';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET NAME DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }

        //Get amount
        if (preg_match('/R\$\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/', $text, $match))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[0]);
        else {
            $this->response['data']['institution'] = 'SANTANDER';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET AMOUNT DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }


        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[1];
        else
        {
            $this->response['data']['institution'] = 'SANTANDER';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET DATE DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get CPF
        if (preg_match('/CPF\s*([\*\,\.\d\-]+)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        else
        {
            $this->response['data']['institution'] = 'SANTANDER';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET CPF DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        //Get timestamp
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        else
        {
            $this->response['data']['institution'] = 'SANTANDER';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET TIMESTAMP DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
            $this->response['status'] = 'NOT_IMPLEMENTED';
        else
            $this->response['status'] = 'SUCCESS';

        $this->response['data']['institution'] = 'SANTANDER';

        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataItau1(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'ITAU1';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataItau2(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'ITAU2';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataSicredi(string $text): array
    {
        printf('=========================================');
        printf('EXTRACTING SICREDI DATA');
        printf('=========================================');

        //Get name
        if (preg_match('/^Solicitante:\s*(.+)$/', $text, $match))
            $this->response['data']['name'] = $match[1];
        else {
            $this->response['data']['institution'] = 'SICREDI';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET NAME DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }

        //Get amount
        if (preg_match('/RS\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/', $text, $match))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[0]);
        else {
            $this->response['data']['institution'] = 'SICREDI';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET AMOUNT DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }

        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[0];
        else
        {
            $this->response['data']['institution'] = 'SICREDI';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET DATE DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get CPF
        if (preg_match('/CPF\s+do\s+pagador:\s+([\d\*\.]+)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        else
        {
            $this->response['data']['institution'] = 'SICREDI';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET CPF DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        //Get timestamp
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        else
        {
            $this->response['data']['institution'] = 'SICREDI';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET TIMESTAMP DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
            $this->response['status'] = 'NOT_IMPLEMENTED';
        else
            $this->response['status'] = 'SUCCESS';

        $this->response['data']['institution'] = 'SICREDI';

        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataNubank(string $text): array
    {
        printf('=========================================');
        printf('EXTRACTING NUBANK DATA');
        printf('=========================================');

        //Get name
        if ((preg_match('/Origem\s+Nome\s+([^\n]+)/', $text, $match)))
            $this->response['data']['name'] = $match[1];
        else {
            $this->response['data']['institution'] = 'NUBANK';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET NAME DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }


        //Get amount
        if ((preg_match('/R\$.(\d.*)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);
        else {
            $this->response['data']['institution'] = 'NUBANK';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET AMOUNT DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }


        //Get date
        if (preg_match('/\d{2} [A-Z]{3} \d{4}\b/', $text, $match))
            $this->response['data']['date'] = $this->formatDateWithTextMonth($match[0]);
        else
        {
            $this->response['data']['institution'] = 'NUBANK';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET DATE DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get CPF
        if (preg_match('/CPF.*?(\d{3})[ .]*\d?[ .]*([\d][ ]?\d[ ]?\d)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/[\s\.]/', '', $match[1]) . preg_replace('/[\s\.]/', '', $match[2]);
        else if(preg_match('/.*?(\d{3})[,](\d{3})/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]);
        else
        {
            $this->response['data']['institution'] = 'NUBANK';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET CPF DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get timestamp
        if (preg_match('/(\d{2} \w{3} \d{4}) - (\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        else
        {
            $this->response['data']['institution'] = 'NUBANK';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET TIMESTAMP DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
            $this->response['status'] = 'NOT_IMPLEMENTED';
        else
            $this->response['status'] = 'SUCCESS';

        $this->response['data']['institution'] = 'NUBANK';

        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataDigio(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'DIGIO';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataC6(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'C6';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataNeon(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'NEON';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataInter(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'INTER';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractData99(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = '99';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataUber(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'UBER';
        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataPicpay(string $text): array
    {
        printf('========================================= \n');
        printf('EXTRACTING PICPAY DATA \n');
        printf('========================================= \n');

        //Get name
        if ((preg_match('/De\s+([A-Z\s]+)/', $text, $match)))
            $this->response['data']['name'] =  str_replace("\n", ' ', $match[1]);
        else
        {
            $this->response['data']['institution'] = 'PICPAY';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET NAME DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        //Get amount
        if ((preg_match('/R\$\s*([\d,.]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);
        if ((preg_match('/RS\s*([\d,.]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);
        else
        {
            $this->response['data']['institution'] = 'PICPAY';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET AMOUNT DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get date
        if (preg_match('/\d{2}\/[a-z]{3}\/\d{4}/', $text, $match))
            $this->response['data']['date'] = $this->formatDateWithTextMonth($match[0]);
        else
        {
            $this->response['data']['institution'] = 'PICPAY';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET DATE DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get CPF
        if (preg_match('/\*\*\*\s*([\d.]+)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/[\s\.]/', '', $match[1]);
        else if(preg_match('/CPF\s*(.*)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        else if(preg_match('/GPF\s*(.*)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        else if(preg_match('/(\d{3})[,](\d{3})/', $text, $match))
            $this->response['data']['middle_cpf'] = $match[1] . $match[2];
        else
        {
            $this->response['data']['institution'] = 'PICPAY';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET CPF DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        //Get timestamp
        if (preg_match('/(\d{2}\/[a-z]{3}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        else
        {
            $this->response['data']['institution'] = 'PICPAY';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET TIMESTAMP DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
            $this->response['status'] = 'NOT_IMPLEMENTED';
        else
            $this->response['status'] = 'SUCCESS';

        $this->response['data']['institution'] = 'PICPAY';

        return $this->response;
    }


    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataIti(string $text): array
    {
        $this->response['status'] = 'NOT_IMPLEMENTED';
        $this->response['data']['institution'] = 'ITI';
        return $this->response;
    }



    /**
     * Example of data extraction method for Bradesco.
     *
     * @param string $text
     * @return array
     */
    private function extractDataNext(string $text): array
    {
        printf('=========================================');
        printf('EXTRACTING NEXT DATA');
        printf('=========================================');

        //Get name
        if ((preg_match('/Dados do pagador\s+Nome:\s*(.+)$/', $text, $match)))
            $this->response['data']['name'] = $match[1];
        else {
            $this->response['data']['institution'] = 'NEXT';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET NAME DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }

        //Get amount
        if ((preg_match('/Valor:\s*R\$ ([\d.,]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);
        else {
            $this->response['data']['institution'] = 'NEXT';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET AMOUNT DATA');
            printf($text);
            json_encode($this->response['data']);
            return $this->response;
        }


        //Get date
        if (preg_match('/Data:\s*(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[1];
        else
        {
            $this->response['data']['institution'] = 'NEXT';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET DATE DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get CPF
        if (preg_match('/CPF:.*?(\d[ .]?\d{2})\.(\d{3})/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]);
        else
        {
            $this->response['data']['institution'] = 'NEXT';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET CPF DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        //Get timestamp
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        else
        {
            $this->response['data']['institution'] = 'NEXT';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET TIMESTAMP DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
            $this->response['status'] = 'NOT_IMPLEMENTED';
        else
            $this->response['status'] = 'SUCCESS';

        $this->response['data']['institution'] = 'NEXT';

        return $this->response;
    }



    /**
     * Example of data extraction method for Banco do Brasil.
     *
     * @param string $text
     * @return array
     */
    private function extractDataGenericReceipt(string $text): array
    {
        printf('=========================================');
        printf('EXTRACTING UNIDENTIFIED INSTITUTION DATA');
        printf('=========================================');

        //Get amount
        if ((preg_match('/RS\s*([\d,.]+)/', $text, $match)) || (preg_match('/R\$\s*([\d,.]+)/', $text, $match)))
            $this->response['data']['amount'] = preg_replace('/[^\d]/', '', $match[1]);
        else
        {
            $this->response['data']['institution'] = 'UNIDENTIFIED_INSTITUTION';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET AMOUNT DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get date
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text, $match))
            $this->response['data']['date'] = $match[1];
        else
        {
            $this->response['data']['institution'] = 'UNIDENTIFIED_INSTITUTION';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET DATE DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }

        //Get CPF
        if (preg_match('/CPF\s*\n(?:ees\s*)([0-9\s\.]+)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/[\s\.]/', '', $match[1]);
        else if(preg_match('/CPF\s*(.*)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        else if(preg_match('/GPF\s*(.*)/', $text, $match))
            $this->response['data']['middle_cpf'] = preg_replace('/\D/', '', $match[1]);
        else
        {
            $this->response['data']['institution'] = 'UNIDENTIFIED_INSTITUTION';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET CPF DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        //Get timestamp
        if (preg_match('/(\d{2}\/[a-z]{3}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        if (preg_match('/(\d{2} \w{3} \d{4}) - (\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/', $text, $match))
            $this->response['data']['timestamp_value_cpf'] = preg_replace('/\D/', '', $match[1]) . preg_replace('/\D/', '', $match[2]) . '_' . $this->response['data']['amount'] . '_' . $this->response['data']['middle_cpf'];
        else
        {
            $this->response['data']['institution'] = 'UNIDENTIFIED_INSTITUTION';
            $this->response['status'] = 'READING_ERROR';
            printf('ERROR IN GET TIMESTAMP DATA');
            printf($text);
            json_encode($this->response['data']);
            return  $this->response;
        }


        if($this->response['data']['amount'] == 0 || $this->response['data']['date'] == '' || $this->response['data']['middle_cpf'] == '' || $this->response['data']['timestamp_value_cpf'] == '')
            $this->response['status'] = 'error';
        else
            $this->response['status'] = 'SUCCESS';


        $this->response['data']['institution'] = 'UNIDENTIFIED_INSTITUTION';
        return $this->response;
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
}
