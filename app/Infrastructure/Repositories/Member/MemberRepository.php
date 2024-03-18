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
    const TABLE_NAME = 'members';
    const ID_COLUMN_JOINED = 'members.id';
    const ID_COLUMN = 'id';
    const ALL_COLUMNS = '*';
    const FULL_NAME_COLUMN = 'full_name';

    const DISPLAY_SELECT_COLUMNS = [
        'members.id as members_id',
        'members.activated as members_activated',
        'members.deleted as members_deleted',
        'members.avatar as members_avatar',
        'members.full_name as members_full_name',
        'members.gender as members_gender',
        'members.cpf as members_cpf',
        'members.rg as members_rg',
        'members.work as members_work',
        'members.born_date as members_born_date',
        'members.email as members_email',
        'members.phone as members_phone',
        'members.cell_phone as members_cell_phone',
        'members.address as members_address',
        'members.district as members_district',
        'members.city as members_city',
        'members.uf as members_uf',
        'members.marital_status as members_marital_status',
        'members.spouse as members_spouse',
        'members.father as members_father',
        'members.mother as members_mother',
        'members.ecclesiastical_function as members_ecclesiastical_function',
        'members.member_type as members_member_type',
        'members.ministries as members_ministries',
        'members.baptism_date as members_baptism_date',
        'members.blood_type as members_blood_type',
        'members.education as members_education'
    ];


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
            'member_type'                 =>  $memberData->memberType,
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
            'member_type'               =>  $memberData->memberType,
            'ministries'                =>  $memberData->ministries,
            'baptism_date'              =>  $memberData->baptismDate,
            'blood_type'                =>  $memberData->bloodType,
            'education'                 =>  $memberData->education,
        ]);
    }
}
