<?php

namespace Infrastructure\Repositories\Member;

use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class MemberRepository extends BaseRepository implements MemberRepositoryInterface
{
    protected mixed $model = Member::class;
    const ID_COLUMN = 'id';
    const ALL_COLUMNS = '*';
    const FULL_NAME_COLUMN = 'full_name';

    /**
     * Array of where, between and another clauses that was mounted dynamically
     */
    private array $queryClausesAndConditions = [
        'where_clause'    =>  [
            'exists' => false,
            'clause'   =>  [],
        ]
    ];

    /**
     * @param MemberData $memberData
     * @return Member
     */
    public function createMember(MemberData $memberData): Member
    {
        return $this->create([
            'activated'                   =>  $memberData->activated,
            'deleted'                     =>  $memberData->deleted,
            'avatar'                      =>  $memberData->avatar,
            'full_name'                   =>  $memberData->fullName,
            'gender'                      =>  $memberData->gender,
            'cpf'                         =>  $memberData->cpf,
            'rg'                          =>  $memberData->rg,
            'work'                        =>  $memberData->work,
            'born_date'                   =>  $memberData->bornDate,
            'email'                       =>  strtolower($memberData->email),
            'phone'                       =>  $memberData->phone,
            'cell_phone'                  =>  $memberData->cellPhone,
            'address'                     =>  $memberData->address,
            'district'                    =>  $memberData->district,
            'city'                        =>  $memberData->city,
            'uf'                          =>  $memberData->uf,
            'marital_status'              =>  $memberData->maritalStatus,
            'spouse'                      =>  $memberData->spouse,
            'father'                      =>  $memberData->father,
            'mother'                      =>  $memberData->mother,
            //'ecclesiastical_function'     =>  $memberData->ecclesiasticalFunction,
            //'ministries'                  =>  $memberData->ministries,
            'baptism_date'                =>  $memberData->baptismDate,
            'blood_type'                  =>  $memberData->bloodType,
            'education'                   =>  $memberData->education,
        ]);
    }


    /**
     * @param null $id
     * @return Collection|Model
     * @throws BindingResolutionException
     */
    public function getMembers($id = null): Collection|Member
    {
        if($id != null)
            return $this->getById($id);
        else
            return $this->getAll(
                self::ALL_COLUMNS,
                self::FULL_NAME_COLUMN,
                BaseRepository::ORDERS['ASC']);
    }


    /**
     * @param null $id
     * @param $status
     * @return int
     * @throws BindingResolutionException
     */
    public function updateStatus($id, $status): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id
        ];

        return $this->update($conditions, ['activated' =>  $status]);
    }


    /**
     * @param null $id
     * @param MemberData $memberData
     * @return bool
     * @throws BindingResolutionException
     */
    public function updateMember($id, MemberData $memberData): mixed
    {
        $conditions = ['field' => self::ID_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => $id,];

        return $this->update($conditions, [
            'activated'                 =>  $memberData->activated,
            'deleted'                   =>  $memberData->activated,
            'avatar'                    =>  $memberData->avatar,
            'full_name'                 =>  $memberData->fullName,
            'gender'                    =>  $memberData->gender,
            'cpf'                       =>  $memberData->cpf,
            'rg'                        =>  $memberData->rg,
            'work'                      =>  $memberData->work,
            'born_date'                 =>  $memberData->bornDate,
            'email'                     =>  strtolower($memberData->email),
            'phone'                     =>  $memberData->phone,
            'cell_phone'                =>  $memberData->cellPhone,
            'address'                   =>  $memberData->address,
            'district'                  =>  $memberData->district,
            'city'                      =>  $memberData->city,
            'uf'                        =>  $memberData->uf,
            'marital_status'            =>  $memberData->maritalStatus,
            'spouse'                    =>  $memberData->spouse,
            'father'                    =>  $memberData->father,
            'mother'                    =>  $memberData->mother,
            'ecclesiastical_function'   =>  $memberData->ecclesiasticalFunction,
            'ministries'                =>  $memberData->ministries,
            'baptism_date'              =>  $memberData->baptismDate,
            'blood_type'                =>  $memberData->bloodType,
            'education'                 =>  $memberData->education,
        ]);
    }
}
