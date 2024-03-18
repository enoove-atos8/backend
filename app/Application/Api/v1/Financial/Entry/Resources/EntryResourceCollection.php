<?php

namespace App\Application\Api\v1\Financial\Entry\Resources;

use Domain\Members\Models\Member;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class EntryResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'entries';


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($item){

            return [
                'id'                            =>  $item->resource->entries_id,
                'entryType'                     =>  $item->resource->entries_entry_type,
                'transactionType'               =>  $item->resource->entries_transaction_type,
                'transactionCompensation'       =>  $item->resource->entries_transaction_compensation,
                'dateTransactionCompensation'   =>  $item->resource->entries_date_transaction_compensation,
                'dateEntryRegister'             =>  $item->resource->entries_date_entry_register,
                'amount'                        =>  $item->resource->entries_amount,
                'devolution'                    =>  $item->resource->entries_devolution,
                'recipient'                     =>  $item->resource->entries_recipient,
                'deleted'                       =>  $item->resource->entries_deleted,
                'comments'                      =>  $item->resource->entries_comments,
                'receipt'                       =>  $item->resource->entries_receipt_link,
                'member'                        =>  $this->getMember($item),
                'reviewer'                      =>  $this->getReviewer($item),
            ];
        });
    }


    /**
     * @param mixed $entry $
     * @return array|null
     */
    public function getMember(mixed $entry): ?array
    {
        if(!is_null($entry->resource->entries_member_id))
        {
            return [
                'id'                  =>  $entry->resource->members_id,
                'activated'           =>  $entry->resource->members_activated,
                'deleted'             =>  $entry->resource->members_deleted,
                'personDataAndIdentification' => [
                    'avatar'        => $entry->resource->members_avatar,
                    'fullName'      => $entry->resource->members_full_name,
                    'gender'        => $entry->resource->members_gender,
                    'cpf'           => $entry->resource->members_cpf,
                    'rg'            => $entry->resource->members_rg,
                    'work'          => $entry->resource->members_work,
                    'bornDate'      => $entry->resource->members_born_date,
                ],
                'addressAndContact' => [
                    'email'         => $entry->resource->members_email,
                    'phone'         => $entry->resource->members_phone,
                    'cellPhone'     => $entry->resource->members_cell_phone,
                    'address'       => $entry->resource->members_address,
                    'district'      => $entry->resource->members_district,
                    'city'          => $entry->resource->members_city,
                    'uf'            => $entry->resource->members_uf,
                ],
                'parentageAndMaritalStatus' => [
                    'maritalStatus'  => $entry->resource->members_marital_status,
                    'spouse'         => $entry->resource->members_spouse,
                    'father'         => $entry->resource->members_father,
                    'mother'         => $entry->resource->members_mother,
                ],
                'ecclesiasticalInformation' => [
                    'baptismDate'               => $entry->resource->members_baptism_date,
                ],
                'otherInformation' => [
                    'bloodType'         => $entry->resource->members_blood_type,
                    'education'         => $entry->resource->members_education,
                ]
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
        if(!is_null($entry->resource->financial_reviewers_id))
        {
            return [
                'id'                 =>  $entry->resource->financial_reviewers_id,
                'fullName'           =>  $entry->resource->financial_reviewers_full_name,
                'reviewer_type'      =>  $entry->resource->financial_reviewers_reviewer_type,
                'avatar'             =>  $entry->resource->financial_reviewers_avatar,
                'gender'             =>  $entry->resource->financial_reviewers_gender,
                'cpf'                =>  $entry->resource->financial_reviewers_cpf,
                'rg'                 =>  $entry->resource->financial_reviewers_rg,
                'email'              =>  $entry->resource->financial_reviewers_email,
                'cellPhone'          =>  $entry->resource->financial_reviewers_cell_phone,
                'activated'          =>  $entry->resource->financial_reviewers_activated,
                'deleted'            =>  $entry->resource->financial_reviewers_deleted,
            ];
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
