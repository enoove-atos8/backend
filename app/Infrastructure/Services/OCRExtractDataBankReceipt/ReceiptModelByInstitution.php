<?php

namespace Infrastructure\Services\OCRExtractDataBankReceipt;

class ReceiptModelByInstitution
{
    private array $response;

    //Entry type consts

    const TITHE_ENTRY_TYPE = 'tithe';
    const DESIGNATED_ENTRY_TYPE = 'designated';
    const OFFERS_ENTRY_TYPE = 'offers';


    // Bank institutions
    const BANK_NUBANK = 'nubank';
    const BANK_PICPAY = 'picpay';
    const BANK_BB = 'bb';
    const BANK_CEF_APP = 'cef_app';
    const BANK_CEF_GER = 'cef_ger';
    const BANK_CEF_IB = 'cef_ib';
    const BANK_CEF_TEM = 'cef_tem';
    const BANK_BRADESCO = 'bradesco';
    const BANK_SANTANDER = 'santander';
    const BANK_ITAU_1 = 'itau_1';
    const BANK_ITAU_2 = 'itau_2';
    const BANK_ITAU_3 = 'itau_3';
    const BANK_ITAU_4 = 'itau_4';
    const BANK_SICREDI = 'sicredi';
    const BANK_DIGIO = 'digio';
    const BANK_INTER = 'inter';
    const BANK_GENERIC = 'generic';
    const BANK_C6 = 'c6';
    const BANK_EFI = 'efi';
    const BANK_NEXT = 'next';


    const CRON_LOG_PATH = 'logs/cron.log';

    public function handleDispatchDataFunctionByInstitution(array $dataExtracted, ?string $entryType, string $depositDate = ''): array | bool
    {
        $this->resetResponseArray();

        $templateReceipt = $dataExtracted['templateReceipt'];
        $text = $dataExtracted['text'];

        return match ($templateReceipt) {
            self::BANK_GENERIC => $this->extractData($text, $entryType, self::BANK_GENERIC),
            self::BANK_NUBANK => $this->extractData($text, $entryType, self::BANK_NUBANK),
            self::BANK_PICPAY => $this->extractData($text, $entryType, self::BANK_PICPAY),
            self::BANK_CEF_APP => $this->extractData($text, $entryType, self::BANK_CEF_APP),
            self::BANK_CEF_GER => $this->extractData($text, $entryType, self::BANK_CEF_GER),
            self::BANK_BRADESCO => $this->extractData($text, $entryType, self::BANK_BRADESCO),
            self::BANK_SANTANDER => $this->extractData($text, $entryType, self::BANK_SANTANDER),
            self::BANK_SICREDI => $this->extractData($text, $entryType, self::BANK_SICREDI),
            self::BANK_NEXT => $this->extractData($text, $entryType, self::BANK_NEXT),
            self::BANK_INTER => $this->extractData($text, $entryType, self::BANK_INTER),
            self::BANK_C6 => $this->extractData($text, $entryType, self::BANK_C6),
            self::BANK_ITAU_1 => $this->extractData($text, $entryType, self::BANK_ITAU_1),
            self::BANK_ITAU_2 => $this->extractData($text, $entryType, self::BANK_ITAU_2),
            self::BANK_ITAU_3 => $this->extractData($text, $entryType, self::BANK_ITAU_3),
            self::BANK_ITAU_4 => $this->extractData($text, $entryType, self::BANK_ITAU_4),
            self::BANK_BB => $this->extractData($text, $entryType, self::BANK_BB),
            default => $this->defaultResponse(),
        };
    }

    private function extractData(string $text, ?string $entryType, string $institution): array
    {
        $this->logExtraction(strtoupper($institution));

        $patterns = $this->getRegexPatterns($institution);

        $this->processExtraction($text, $entryType, $patterns);
        $this->setResponseStatus($entryType, strtoupper($institution));

        return $this->response;
    }

    private function processExtraction(string $text, ?string $entryType, array $patterns): void
    {
        if(key_exists('amount', $patterns))
            $this->extractField($text, $patterns['amount'], 'amount');

        if(key_exists('date', $patterns))
            $this->extractField($text, $patterns['date'], 'date');

        if ($entryType === self::TITHE_ENTRY_TYPE) {
            if(key_exists('cpf', $patterns))
                $this->extractField($text, $patterns['cpf'], 'middle_cpf');

            $this->extractTimestampAndMountTimestampValueCpf($text, $entryType, $patterns['timestamp']);
        } else {
            $this->extractTimestampAndMountTimestampValueCpf($text, null, $patterns['timestamp']);
        }
    }

    private function getRegexPatterns(string $institution): array
    {
        return match ($institution) {
            self::BANK_GENERIC => [
                'amount' => [
                    '/R\$\s*([\d,.]+)/',
                ],
                'date' => [
                    '/\d{2}\/\d{2}\/\d{4}/',
                ],
                'cpf' => [
                    '/CPF.*?(\d{3})[,.]*\d?[,.]*([\d][ ]?\d[ ]?\d)/',
                    '/.*?(\d{3})[,](\d{3})/'
                ],
                'timestamp' => '/(\d{2} \w{3} \d{4}) - (\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_NUBANK => [
                'amount' => ['/R\$.(\d.*)/'],
                'name' => ['/Origem\s+Nome\s+([^\n]+)/'],
                'date' => ['/\d{2} [A-Z]{3} \d{4}/'],
                'cpf' => [
                    '/CPF.*?(\d{3}\.\d{3})/',
                ],
                'timestamp' => '/(\d{2} \w{3} \d{4}) - (\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_PICPAY => [
                'amount' => [
                    '/R\$\s*([\d,.]+)/',
                    '/RS\s*([\d,.]+)/'
                ],
                'name' => ['/De\s+([A-Z\s]+)/'],
                'date' => ['/\\d{2}\/[a-z]{3}\/\\d{4}/'],
                'cpf' => [
                    '/\*\*\*\s*([\d.]+)/',
                    '/CPF\s*(.*)/',
                    '/GPF\s*(.*)/',
                    '/(\d{3})[,](\d{3})/'
                ],
                'timestamp' => '/(\d{2}\/[a-z]{3}\/\\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_CEF_APP => [
                'amount' => [
                    '/RS\s*([\d,.]+)/',
                    '/R\$\s*([\d,.]+)/'
                ],
                'name' => ['/Dados do pagador\s+Nome\s+([^\n]+)/'],
                'date' => ['/(\d{2}\/\d{2}\/\d{4})/'],
                'cpf' => [
                    '/CPF\s*\n(?:ees\s*)([0-9\s\.]+)/',
                    '/CPF\s*(.*)/',
                    '/GPF\s*(.*)/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_CEF_GER => [
                'amount' => [
                    '/R\$\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/',
                ],
                'name' => ['/Origem\s+Nome:\s*(.+)$/'],
                'date' => ['/(\d{2}\/\d{2}\/\d{4}) as/'],
                'cpf' => [
                    '/CPF:\s?(\d{11})/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_BRADESCO => [
                'amount' => [
                    '/R\$\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/',
                ],
                'name' => ['/Dados de quem pagou\s+Nome:\s*(.+)$/'],
                'date' => ['/(\d{2}\/\d{2}\/\d{4})/'],
                'cpf' => [
                    '/CPF:\s*([\*\.0-9\-]+)/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_SANTANDER => [
                'amount' => [
                    '/R\$\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/',
                ],
                'name' => ['/^De:\s*(.+)$/'],
                'date' => ['/(\d{2}\/\d{2}\/\d{4})/'],
                'cpf' => [
                    '/CPF\s*([\*\,\.\d\-]+)/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_SICREDI => [
                'amount' => [
                    '/RS\s?\d{1,3}(?:\.\d{3})*(?:,\d{2})/',
                ],
                'name' => ['/^Solicitante:\s*(.+)$/'],
                'date' => ['/(\d{2}\/\d{2}\/\d{4})/'],
                'cpf' => [
                    '/CPF\s+do\s+pagador:\s+([\d\*\.]+)/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_NEXT => [
                'amount' => [
                    '/Valor:\s*R\$ ([\d.,]+)/',
                ],
                'name' => ['/Dados do pagador\s+Nome:\s*(.+)$/'],
                'date' => ['/Data:\s*(\d{2}\/\d{2}\/\d{4})/'],
                'cpf' => [
                    '/CPF:.*?(\d[ .]?\d{2})\.(\d{3})/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_INTER => [
                'amount' => [
                    '/RS\s(\d{1,3}(?:\.\d{3})*,\d{2})/',
                    '/R\$ \d{1,3}(?:\.\d{3})*,\d{2}/',
                ],
                'date' => ['/\d{2}\/\d{2}\/\d{4}/'],
                'cpf' => [
                    '/Quem pagou[\s\S]*?(\d{3}\.\d{3})/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[\s\S]*?(\d{2}h\d{2})/'
            ],
            self::BANK_C6 => [
                'amount' => [
                    '/Valor\s*R\$\s*([\d.,]+)/',
                    '/valor\s*R\$\s*([\d.,]+)/',
                    '/R\$ \d{1,3}(?:\.\d{3})*,\d{2}/',
                ],
                'date' => ['/\d{2}\/\d{2}\/\d{4}/'],
                'timestamp' => [
                    '/(\d{2}\/\d{2}\/\d{4})[^0-9]*(\d{2}:\d{2})/',
                    '/(\d{2}\/\d{2}\/\d{4}).*\n(\d{2}:\d{2})/'
                ]
            ],
            self::BANK_ITAU_2 => [
                'amount' => [
                    '/RS\s(\d{1,3}(?:\.\d{3})*,\d{2})/',
                    '/R\$ (\d{1,3}(?:\.\d{3})*,\d{2})/',
                    '/rs (\d{1,3}(?:\.\d{3})*,\d{2})/',
                ],
                'name' => [
                    '/de\n+(.*?)\n/',
                    '/nome\s+([A-Za-zÀ-ÿ\s]+)(?=\s+cpf)/',
                ],
                'date' => [
                    '/(\d{2}\/\d{2}\/\d{4})/',
                    '/realizadoem\s+(\d{2}\/\d{2}\/\d{4})/',
                ],
                'cpf' => [
                    '/CPF\s*([\*\,\.\d\-]+)/',
                    '/cpf\s+\D*(\d{3}\.\d{3})/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_ITAU_3 => [
                'amount' => [
                    '/RS\s(\d{1,3}(?:\.\d{3})*,\d{2})/',
                    '/R\$ (\d{1,3}(?:\.\d{3})*,\d{2})/',
                ],
                'name' => [
                    '/de\n+(.*?)\n/',
                    '/nome\s+([A-Za-zÀ-ÿ\s]+)(?=\s+cpf)/',
                ],
                'date' => [
                    '/(\d{2}\/\d{2}\/\d{4})/',
                    '/realizadoem\s+(\d{2}\/\d{2}\/\d{4})/',
                ],
                'cpf' => [
                    '/CPF\s*([\*\,\.\d\-]+)/',
                    '/cpf\s+\D*(\d{3}\.\d{3})/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_ITAU_4 => [
                'amount' => [
                    '/RS\s(\d{1,3}(?:\.\d{3})*,\d{2})/',
                    '/R\$ (\d{1,3}(?:\.\d{3})*,\d{2})/',
                ],
                'name' => [
                    '/de\n+(.*?)\n/',
                    '/nome\s+([A-Za-zÀ-ÿ\s]+)(?=\s+cpf)/',
                ],
                'date' => [
                    '/(\d{2}\/\d{2}\/\d{4})/',
                    '/realizadoem\s+(\d{2}\/\d{2}\/\d{4})/',
                ],
                'cpf' => [
                    '/CPF\s*([\*\,\.\d\-]+)/',
                    '/cpf\s+\D*(\d{3}\.\d{3})/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})[^\d]*(\d{2}:\d{2}:\d{2})/'
            ],
            self::BANK_BB => [
                'amount' => [
                    '/R\$\s*([0-9]+\s*,\s*[0-9]{2})/',
                ],
                'date' => [
                    '/\b(\d{2}\/\d{2}\/\d{4})\b/',
                ],
                'cpf' => [
                    '/CPF\s+DO\s+PAGADOR:\s+#([0-9\s\.\-]+)/',
                ],
                'timestamp' => '/(\d{2}\/\d{2}\/\d{4})\s*-\s*(\d{2}:\d{2}:\d{2})/'
            ],
            default => [],
        };
    }

    private function extractField(string $text, array|string $patterns, string $field): void
    {
        foreach ((array)$patterns as $pattern) {
            if (preg_match($pattern, $text, $match))
            {
                $index = count($match) > 1 ? 1 : 0;
                $value = $match[$index];

                if ($field === 'amount')
                    $this->response['data'][$field] = (int)preg_replace('/[^\d]/', '', $value);
                elseif ($field === 'date')
                    $this->response['data'][$field] = $this->formatDateWithTextMonth(trim($value));
                elseif ($field === 'middle_cpf')
                    $this->response['data'][$field] = preg_replace('/\D/', '', $value);
                else
                    $this->response['data'][$field] = trim($value);

                return;
            }
        }
    }

    private function extractTimestampAndMountTimestampValueCpf(string $text, ?string $entryType, array|string $patterns): void
    {
        if(is_string($patterns)){
            if (preg_match($patterns, $text, $match)) {
                $timestamp = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]);
                $this->response['data']['timestamp_value_cpf'] = $timestamp . '_' . $this->response['data']['amount'];

                if ($entryType === self::TITHE_ENTRY_TYPE && $this->response['data']['middle_cpf'] != '') {
                    $this->response['data']['timestamp_value_cpf'] .= '_' . $this->response['data']['middle_cpf'];
                }
            }
        }
        if(is_array($patterns))
        {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $text, $match)) {
                    $timestamp = preg_replace('/\D/', '', $this->formatDateWithTextMonth($match[1])) . preg_replace('/\D/', '', $match[2]);
                    $this->response['data']['timestamp_value_cpf'] = $timestamp . '_' . $this->response['data']['amount'];

                    if ($entryType === self::TITHE_ENTRY_TYPE && $this->response['data']['middle_cpf'] != '') {
                        $this->response['data']['timestamp_value_cpf'] .= '_' . $this->response['data']['middle_cpf'];
                    }

                    return;
                }
            }
        }
    }

    private function setResponseStatus(?string $entryType, string $institution): void
    {
        if($entryType === self::TITHE_ENTRY_TYPE)
        {
            if ($this->response['data']['amount'] !== 0 && !empty($this->response['data']['date']) && !empty($this->response['data']['middle_cpf']) && !empty($this->response['data']['timestamp_value_cpf']))
                $this->response['status'] = 'SUCCESS';

            else if ($this->response['data']['amount'] !== 0 && !empty($this->response['data']['date']))
                $this->response['status'] = 'SUCCESS';

            else
                $this->response['status'] = 'READING_ERROR';
        }
        else if($entryType === self::DESIGNATED_ENTRY_TYPE)
        {
            if ($this->response['data']['amount'] !== 0 && !empty($this->response['data']['date']) && !empty($this->response['data']['timestamp_value_cpf']))
                $this->response['status'] = 'SUCCESS';

            else
                $this->response['status'] = 'READING_ERROR';
        }

        $this->response['data']['institution'] = $institution;
    }

    private function logExtraction(string $institution): void
    {
        echo(PHP_EOL . '=========================================' . PHP_EOL);
        echo("EXTRACTING {$institution} DATA" . PHP_EOL);
        echo('=========================================' . PHP_EOL);
    }

    private function defaultResponse(): array
    {
        return [
            'status' => 'NOT_IMPLEMENTED',
            'msg' => '',
            'data' => [
                'institution' => 'NOT_MAPPED',
            ]
        ];
    }

    private function formatDateWithTextMonth(string $date): string
    {
        $arrMonthTextToNumber = [
            'jan' => '01', 'fev' => '02', 'mar' => '03', 'abr' => '04',
            'mai' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08',
            'set' => '09', 'out' => '10', 'nov' => '11', 'dez' => '12',
            'janeiro' => '01', 'fevereiro' => '02', 'março' => '03',
            'abril' => '04', 'maio' => '05', 'junho' => '06',
            'julho' => '07', 'agosto' => '08', 'setembro' => '09',
            'outubro' => '10', 'novembro' => '11', 'dezembro' => '12',
        ];

        $arrDateParts = preg_split('/[\/ ]/', $date);
        $day = $arrDateParts[0];
        $monthText = strtolower($arrDateParts[1]);

        if(key_exists($monthText, $arrMonthTextToNumber))
        {
            $monthNumber = $arrMonthTextToNumber[$monthText];
            $year = $arrDateParts[2];

            return $day . '/' . $monthNumber . '/' . $year;
        }
        else
        {
            return $date;
        }

    }

    private function resetResponseArray(): void
    {
        $this->response = [
            'status' => '',
            'msg' => '',
            'data' => [
                'name' => '',
                'amount' => 0,
                'date' => '',
                'cpf' => '',
                'middle_cpf' => '',
                'cnpj' => '',
                'institution' => '',
                'timestamp_value_cpf' => '',
            ]
        ];
    }
}
