<?php

namespace Application\Api\v1\Financial\Entries\Entries\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class DevolutionEntriesResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'data';
    private string $yearMonth = '';


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $yearMonth = [];
        $result = [];

        foreach ($this->collection as $item)
        {
            $currentMonth = substr($item->entries_date_entry_register, 0, 7);

            if(!in_array($currentMonth, $yearMonth))
            {
                $yearMonth[] = substr($item->entries_date_entry_register, 0, 7);

                $result[] = [
                    'dateEntryRegister' => $currentMonth,
                    'monthName'         => $this->getMonthName($currentMonth),
                    'devolutionEntries'    =>  $this->filterCollectionByMonth($this, $currentMonth),
                ];
            }
        }

        return [
            'qtdEntries'    =>  $this->resource->count(),
            'totalAmount'   =>  $this->resource->sum('entries_amount'),
            'entries'       =>  $result
        ];
    }


    /**
     * @param mixed $data
     * @param string $filterValue
     * @return array
     */
    public function filterCollectionByMonth(mixed $data, string $filterValue): array
    {
        $result = [];
        $arrCollection = $data->collection->filter(function ($item) use ($filterValue){
            if(str_contains($item->entries_date_entry_register, $filterValue))
                return $item;
        })->toArray();

        foreach ($arrCollection as $value)
        {
            $result[] = [
                'id'                            =>  $value->entries_id,
                'entryType'                     =>  $value->entries_entry_type,
                'transactionType'               =>  $value->entries_transaction_type,
                'transactionCompensation'       =>  $value->entries_transaction_compensation,
                'dateTransactionCompensation'   =>  $value->entries_date_transaction_compensation,
                'dateEntryRegister'             =>  $value->entries_date_entry_register,
                'amount'                        =>  $value->entries_amount,
                'devolution'                    =>  $value->entries_devolution,
                'residualValues'                =>  $value->entries_residual_value,
                'recipient'                     =>  $value->entries_recipient,
                'deleted'                       =>  $value->entries_deleted,
                'comments'                      =>  $value->entries_comments,
                'receipt'                       =>  $value->entries_receipt_link,
                'member'                        =>  null,
                'reviewer'                      => $this->getReviewer($value),

            ];
        }

        return $result;
    }



    /**
     * @param mixed $entry $
     * @return array|null
     */
    public function getReviewer(mixed $entry): ?array
    {
        if(!is_null($entry->financial_reviewers_id))
        {
            return [
                'id'                 =>  $entry->financial_reviewers_id,
                'fullName'           =>  $entry->financial_reviewers_full_name,
                'reviewer_type'      =>  $entry->financial_reviewers_reviewer_type,
                'avatar'             =>  $entry->financial_reviewers_avatar,
                'gender'             =>  $entry->financial_reviewers_gender,
                'cpf'                =>  $entry->financial_reviewers_cpf,
                'rg'                 =>  $entry->financial_reviewers_rg,
                'email'              =>  $entry->financial_reviewers_email,
                'cellPhone'          =>  $entry->financial_reviewers_cell_phone,
                'activated'          =>  $entry->financial_reviewers_activated,
                'deleted'            =>  $entry->financial_reviewers_deleted,
            ];
        }
        else
        {
            return null;
        }
    }



    /**
     * @param mixed $date
     * @return string
     */
    public function getMonthName(mixed $date): string
    {
        $monthIndex = intval(substr($date, 5, 6)) - 1;
        $monthNames = [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
        ];

        return $monthNames[$monthIndex];
    }
}
