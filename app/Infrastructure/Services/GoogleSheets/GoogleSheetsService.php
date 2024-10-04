<?php

namespace Infrastructure\Services\GoogleSheets;

use Google\Service\Exception;
use Google\Service\Sheets;

class GoogleSheetsService
{
    const CULT_DATE_RANGE = 'G5:G6';
    const CULT_DEPOSIT_DATE_RANGE = 'H5:H6';
    const CULT_TITHES_NAMES_VALUES_RANGE = 'B10:I29';
    const CULT_DESIGNATED_VALUES_IDS_RANGE = 'H33:I37';
    const CULT_OFFERS_VALUES_RANGE = 'I39';
    const VALUE_BY_RECEIPT_RANGE = 'J2:J8';
    const TOTAL_AMOUNT_RANGE = 'I41';


    /**
     * @param $client
     * @param $spreadSheetId
     * @param $range
     * @return array
     * @throws Exception
     */
    public function readSheet($client, $spreadSheetId, $range): array
    {
        $sheetsService = new Sheets($client);
        $response = $sheetsService->spreadsheets_values->get($spreadSheetId, $range);
        $values = $response->getValues();

        if (is_null($values)) {
            return [];
        }

        $filteredValues = array_map(function($row) {
            return array_filter($row, function($value) {
                return !empty($value);
            });
        }, $values);


        $filteredValues = array_filter($filteredValues);

        return $filteredValues;
    }


    /**
     * @param $client
     * @param $spreadSheetId
     * @return array
     * @throws Exception
     */
    public function readTithesBlock($client, $spreadSheetId): array
    {
        $tithesBlock = $this->readSheet($client, $spreadSheetId, self::CULT_TITHES_NAMES_VALUES_RANGE);

        return array_values($tithesBlock[0]) ?? [];
    }


    /**
     * @param $client
     * @param $spreadSheetId
     * @return array
     * @throws Exception
     */
    public function readDesignatedBlock($client, $spreadSheetId): array
    {
        $designatedBlock = $this->readSheet($client, $spreadSheetId, self::CULT_DESIGNATED_VALUES_IDS_RANGE);

        return $designatedBlock ?? [];
    }


    /**
     * @param $client
     * @param $spreadSheetId
     * @return array
     * @throws Exception
     */
    public function readOffersBlock($client, $spreadSheetId): array
    {
        $offerValue = $this->readSheet($client, $spreadSheetId, self::CULT_OFFERS_VALUES_RANGE);

        return $offerValue ?? [];
    }


    /**
     * @param $client
     * @param $spreadSheetId
     * @return array|null
     * @throws Exception
     */
    public function getDepositDate($client, $spreadSheetId): array | null
    {
        return $this->readSheet($client, $spreadSheetId, self::CULT_DEPOSIT_DATE_RANGE);
    }



    /**
     * @param $client
     * @param $spreadSheetId
     * @return array|null
     * @throws Exception
     */
    public function getCultDate($client, $spreadSheetId): array | null
    {
        return $this->readSheet($client, $spreadSheetId, self::CULT_DATE_RANGE);
    }


    /**
     * @param $client
     * @param $spreadSheetId
     * @return array|null
     * @throws Exception
     */
    public function getReceiptsCountsValues($client, $spreadSheetId): array | null
    {
        return $this->readSheet($client, $spreadSheetId, self::VALUE_BY_RECEIPT_RANGE);
    }



    /**
     * @param $client
     * @param $spreadSheetId
     * @return array|null
     * @throws Exception
     */
    public function getTotalValue($client, $spreadSheetId): array | null
    {
        return $this->readSheet($client, $spreadSheetId, self::TOTAL_AMOUNT_RANGE);
    }
}
