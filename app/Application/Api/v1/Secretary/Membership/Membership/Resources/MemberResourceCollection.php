<?php

namespace Application\Api\v1\Secretary\Membership\Membership\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class MemberResourceCollection extends ResourceCollection
{
    private ?int $countRows;

    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     *
     * @var string
     */
    public static $wrap = 'members';

    public function __construct($resource, ?int $countRows = null)
    {
        parent::__construct($resource);

        $this->countRows = $countRows;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($item) {

            $ministries = [];
            $ecclesiasticalFunction = [];

            return [
                'id' => $item->id,
                'activated' => $item->activated,
                'deleted' => $item->deleted,
                'personDataAndIdentification' => [
                    'avatar' => $item->avatar,
                    'fullName' => $item->fullName,
                    'gender' => $item->gender,
                    'cpf' => $item->cpf,
                    'rg' => $item->rg,
                    'work' => $item->work,
                    'bornDate' => $item->bornDate,
                ],
                'addressAndContact' => [
                    'email' => $item->email,
                    'phone' => $item->phone,
                    'cellPhone' => $item->cellPhone,
                    'address' => $item->address,
                    'district' => $item->district,
                    'city' => $item->city,
                    'uf' => $item->uf,
                ],
                'parentageAndMaritalStatus' => [
                    'maritalStatus' => $item->maritalStatus,
                    'spouse' => $item->spouse,
                    'father' => $item->father,
                    'mother' => $item->mother,
                ],
                'ecclesiasticalInformation' => [
                    'ecclesiasticalFunction' => $ecclesiasticalFunction,
                    'ministries' => $ministries,
                    'memberType' => $item->memberType,
                    'baptismDate' => $item->baptismDate,
                    'groupIds' => $item->groupIds,
                ],
                'otherInformation' => [
                    'bloodType' => $item->bloodType,
                    'education' => $item->education,
                    'dependentsMembers' => $item->dependentsMembers ?? [],
                ],
                'titheAmount' => $item->titheAmount,
                'titheHistory' => $item->titheHistory ?? [
                    'isDependent' => false,
                    'history' => [],
                ],
            ];
        });
    }

    public function with($request): array
    {
        return [
            'total' => count($this),
            'countRows' => $this->countRows,
        ];
    }
}
