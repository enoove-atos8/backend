<?php

namespace Infrastructure\Services\GoogleSheets;

use Google\Service\Exception;
use Google\Service\Sheets;

class GoogleSheetsService
{
    const CULT_DATE_RANGE = 'H5:H6';
    const CULT_DEPOSIT_DATE_RANGE = 'I5:I6';
    const CULT_TITHES_NAMES_VALUES_RANGE = 'B10:I29';
    const CULT_DESIGNATED_VALUES_IDS_RANGE = 'I33:J37';
    const CULT_OFFERS_VALUES_RANGE = 'I39';

    const SPREADSHEET_ID = '';


    /**
     * @param $client
     * @param $spreadSheetId
     * @param $range
     * @return array[]
     * @throws Exception
     */
    public function readSheet($client, $spreadSheetId, $range): array
    {
        $sheetsService = new Sheets($client);
        $response = $sheetsService->spreadsheets_values->get($spreadSheetId, $range);
        $values = $response->getValues();

        $filteredValues = array_map(function($row) {
            return array_filter($row, function($value) {
                return !empty($value);
            });
        }, $values);


        $filteredValues = array_filter($filteredValues);

        return $filteredValues;
    }



    /**
     * @return array[]
     * @throws Exception
     */
    public function readTithesBlock($client, $spreadSheetId): array
    {
        return $this->readSheet($client, $spreadSheetId, self::CULT_TITHES_NAMES_VALUES_RANGE);
    }



    /**
     * @throws Exception
     */
    public function readDesignatedBlock($client, $spreadSheetId): array
    {
        return $this->readSheet($client, $spreadSheetId, self::CULT_DESIGNATED_VALUES_IDS_RANGE);
    }



    /**
     * @throws Exception
     */
    public function readOffersBlock($client, $spreadSheetId): array
    {
        return $this->readSheet($client, $spreadSheetId, self::CULT_OFFERS_VALUES_RANGE);
    }
}
