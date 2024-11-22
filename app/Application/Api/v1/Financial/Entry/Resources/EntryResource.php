<?php

namespace App\Application\Api\v1\Financial\Entry\Resources;

use Domain\Members\Models\Member;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EntryResource extends JsonResource
{
    public static $wrap = 'entry';
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $entry = $this->resource;

        return [
            'id'                            =>  $entry->id,
            'member'                        =>  $this->getMember($entry),
            'reviewer'                      =>  $this->getReviewer($entry),
            'cultId'                        =>  $entry->cult_id,
            'groupReturnedId'               =>  $entry->group_returned_id,
            'groupReceivedId'               =>  $entry->group_received_id,
            'identificationPending'         =>  $entry->identification_pending,
            'entryType'                     =>  $entry->entry_type,
            'transactionType'               =>  $entry->transaction_type,
            'transactionCompensation'       =>  $entry->transaction_compensation,
            'dateTransactionCompensation'   =>  $entry->date_transaction_compensation,
            'dateEntryRegister'             =>  $entry->date_entry_register,
            'amount'                        =>  $entry->amount,
            'timestampValueCpf'             =>  $entry->timestamp_value_cpf,
            'devolution'                    =>  $entry->devolution,
            'residualValue'                 =>  $entry->residual_value,
            'deleted'                       =>  $entry->deleted,
            'comments'                      =>  $entry->comments,
            'receipt'                       =>  $entry->receipt_link,
        ];
    }



    /**
     * @param mixed $data
     * @return array|null
     */
    public function getMember(mixed $data): ?array
    {
        $member = $data->member()->first();

        if(!is_null($member))
        {
            return [
                'id'                  =>  $member->id,
                'activated'           =>  $member->activated,
                'deleted'             =>  $member->deleted,
                'personDataAndIdentification' => [
                    'avatar'        => $member->avatar,
                    'fullName'      => $member->full_name,
                    'gender'        => $member->gender,
                    'cpf'           => $member->cpf,
                    'rg'            => $member->rg,
                    'work'          => $member->work,
                    'bornDate'      => $member->born_date,
                ],
                'addressAndContact' => [
                    'email'         => $member->email,
                    'phone'         => $member->phone,
                    'cellPhone'     => $member->cell_phone,
                    'address'       => $member->address,
                    'district'      => $member->district,
                    'city'          => $member->city,
                    'uf'            => $member->uf,
                ],
                'parentageAndMaritalStatus' => [
                    'maritalStatus'  => $member->marital_status,
                    'spouse'         => $member->spouse,
                    'father'         => $member->father,
                    'mother'         => $member->mother,
                ],
                'ecclesiasticalInformation' => [
                    'baptismDate'               => $member->baptism_date,
                ],
                'otherInformation' => [
                    'bloodType'         => $member->blood_type,
                    'education'         => $member->education,
                ]
            ];
        }
        else
        {
            return null;
        }
    }



    /**
     * @param mixed $data
     * @return array|null
     */
    public function getReviewer(mixed $data): ?array
    {
        $reviewerId = $data->reviewer_id;
        $reviewerMember = Member::find($reviewerId);

        if(!is_null($reviewerMember))
        {
            return [
                'id'                  =>  $reviewerMember->id,
                'activated'           =>  $reviewerMember->activated,
                'deleted'             =>  $reviewerMember->deleted,
                'personDataAndIdentification' => [
                    'avatar'        => $reviewerMember->avatar,
                    'fullName'      => $reviewerMember->full_name,
                    'gender'        => $reviewerMember->gender,
                    'cpf'           => $reviewerMember->cpf,
                    'rg'            => $reviewerMember->rg,
                    'work'          => $reviewerMember->work,
                    'bornDate'      => $reviewerMember->born_date,
                ],
            ];
        }
        else
        {
            return null;
        }
    }
}
