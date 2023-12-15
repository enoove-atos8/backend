<?php

namespace Application\Api\v1\Entry\Resources;

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
        return $this->collection->map(function ($item) {

            return [
                'id'                            =>  $item->id,
                'entryType'                     =>  $item->entry_type,
                'transactionType'               =>  $item->transaction_type,
                'transactionCompensation'       =>  $item->transaction_compensation,
                'dateTransactionCompensation'   =>  $item->date_transaction_compensation,
                'dateEntryRegister'             =>  $item->date_entry_register,
                'amount'                        =>  $item->amount,
                'devolution'                    =>  $item->devolution,
                'recipient'                     =>  $item->recipient,
                'deleted'                       =>  $item->deleted,
                'member'                        =>  $this->getMember($item),
                'reviewer'                      =>  $this->getReviewer($item),
            ];
        });
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


    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
