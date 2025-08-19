<?php

namespace Application\Api\v1\Financial\Entries\Entries\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JsonSerializable;

class EntryResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'entries';
    private null | Collection $groups;


    public function __construct($resource, $groups = null)
    {
        parent::__construct($resource);
        $this->groups = $groups;
    }


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];
        $totalGeneral = 0;
        $entries = $this->collection;

        foreach ($entries as $item)
        {
            $result[] = [
                'id'                            =>  $item->entries_id,
                'member'                        =>  $this->getMember($item),
                'account'                        =>  $this->getAccount($item),
                'reviewer'                      =>  $this->getReviewer($item),
                'cultId'                        =>  $item->entries_cult_id,
                'groupReturned'                 =>  $this->getGroup($this->groups, $item->entries_group_returned_id),
                'groupReceived'                 =>  $this->getGroup($this->groups, $item->entries_group_received_id),
                'identificationPending'         =>  $item->entries_identification_pending,
                'entryType'                     =>  $item->entries_entry_type,
                'transactionType'               =>  $item->entries_transaction_type,
                'transactionCompensation'       =>  $item->entries_transaction_compensation,
                'dateTransactionCompensation'   =>  $item->entries_date_transaction_compensation,
                'dateEntryRegister'             =>  $item->entries_date_entry_register,
                'amount'                        =>  $item->entries_amount,
                'timestampValueCpf'             =>  $item->entries_timestamp_value_cpf,
                'devolution'                    =>  $item->entries_devolution,
                'residualValue'                 =>  $item->entries_residual_value,
                'deleted'                       =>  $item->entries_deleted,
                'comments'                      =>  $item->entries_comments,
                'receipt'                       =>  $item->entries_receipt_link,
            ];

            $totalGeneral += floatval($item->entries_amount);
        }

        return [
            'data'          =>  $result,
            'totalGeneral'  =>  $totalGeneral
        ];
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
     * @param mixed $entry $
     * @return array|null
     */
    public function getAccount(mixed $entry): ?array
    {
        if(!is_null($entry->accounts_id))
        {
            return [
                'id'            =>  $entry->accounts_id,
                'accountType'   =>  $entry->accounts_account_type,
                'bankName'      =>  $entry->accounts_bank_name,
                'agencyNumber'  =>  $entry->accounts_agency_number,
                'accountNumber' =>  $entry->accounts_account_number,
                'activated'     =>  $entry->accounts_activated,
            ];
        }
        else
        {
            return null;
        }
    }


    /**
     * @param mixed $entry
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
     * @param mixed $groups
     * @param $groupId
     * @return array|null
     */
    public function getGroup(mixed $groups, $groupId): ?array
    {
        $result = [];

        if(!is_null($groupId))
        {
            $filtered = $groups->filter(function ($item) use ($groupId) {
                if($item->groups_id === $groupId)
                    return $item;
            })->toArray();

            foreach ($filtered as $group)
            {
                $result = [
                    'id'        =>  $group->groups_id,
                    'name'      =>  $group->groups_name,
                    'enabled'   =>  $group->groups_enabled,
                ];
            }

            return $result;
        }
        else
        {
            return null;
        }
    }


    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
