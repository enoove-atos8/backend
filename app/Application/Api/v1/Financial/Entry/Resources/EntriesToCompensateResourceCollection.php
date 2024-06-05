<?php

namespace Application\Api\v1\Financial\Entry\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JsonSerializable;

class EntriesToCompensateResourceCollection extends ResourceCollection
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
                    'entriesOfMonth'    =>  $this->filterCollectionByMonth($this, $currentMonth),
                ];
            }
        }

        return [
            'qtdEntries'    =>  $this->collection->count(),
            'totalAmount'   =>  $this->collection->sum('entries_amount'),
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
                'amount'                        =>  $value->entries_amount,
                'dateEntryRegister'             =>  $value->entries_date_entry_register,
                'dateTransactionCompensation'   =>  $value->entries_date_transaction_compensation,
                'transactionType'               =>  $value->entries_transaction_type,
                'deleted'                       =>  $value->entries_deleted,
                'devolution'                    =>  $value->entries_devolution,
                'residual_value'                =>  $value->entries_residual_value,
                'entryType'                     =>  $value->entries_entry_type,
                'member'                        => $this->getMember($value)
            ];
        }

        return $result;
    }



    /**
     * @param mixed $entry $
     * @return array|null
     */
    public function getMember(mixed $entry): ?array
    {
        if(!is_null($entry->entries_member_id))
        {
            return [
                'id'                  =>  $entry->members_id,
                'activated'           =>  $entry->members_activated,
                'deleted'             =>  $entry->members_deleted,
                'personDataAndIdentification' => [
                    'avatar'        => $entry->members_avatar,
                    'fullName'      => $entry->members_full_name,
                    'gender'        => $entry->members_gender,
                    'cpf'           => $entry->members_cpf,
                    'rg'            => $entry->members_rg,
                    'work'          => $entry->members_work,
                    'bornDate'      => $entry->members_born_date,
                ],
                'addressAndContact' => [
                    'email'         => $entry->members_email,
                    'phone'         => $entry->members_phone,
                    'cellPhone'     => $entry->members_cell_phone,
                    'address'       => $entry->members_address,
                    'district'      => $entry->members_district,
                    'city'          => $entry->members_city,
                    'uf'            => $entry->members_uf,
                ],
                'parentageAndMaritalStatus' => [
                    'maritalStatus'  => $entry->members_marital_status,
                    'spouse'         => $entry->members_spouse,
                    'father'         => $entry->members_father,
                    'mother'         => $entry->members_mother,
                ],
                'ecclesiasticalInformation' => [
                    'baptismDate'               => $entry->members_baptism_date,
                ],
                'otherInformation' => [
                    'bloodType'         => $entry->members_blood_type,
                    'education'         => $entry->members_education,
                ]
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
