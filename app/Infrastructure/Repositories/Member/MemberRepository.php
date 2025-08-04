<?php

namespace Infrastructure\Repositories\Member;

;

use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Domain\Secretary\Membership\Models\Member;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class MemberRepository extends BaseRepository implements MemberRepositoryInterface
{
    protected mixed $model = Member::class;
    const TABLE_NAME = 'members';
    const MEMBER_VALUE = 'member';
    const CONGREGATE_VALUE = 'congregate';

    const ID_COLUMN_JOINED = 'members.id';
    const MEMBER_TYPE_COLUMN_JOINED = 'members.member_type';
    const MEMBER_TYPE_COLUMN = 'member_type';
    const PAGINATE_NUMBER = 30;

    const DELETED_COLUMN = 'deleted';
    const MEMBER_GENDER_COLUMN_JOINED = 'members.gender';
    const MEMBER_GENDER_COLUMN = 'gender';
    const ID_COLUMN = 'id';
    const ACTIVATED_COLUMN = 'activated';
    const MIDDLE_CPF_COLUMN = 'middle_cpf';
    const CPF_COLUMN = 'cpf';
    const ALL_COLUMNS = '*';
    const FULL_NAME_COLUMN = 'full_name';
    const BORN_DATE_COLUMN = 'born_date';
    const ECCLESIASTICAL_DIVISIONS_GROUP_ID_COLUMN = 'ecclesiastical_divisions_group_id';
    const GROUP_LEADER_COLUMN = 'group_leader';

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
            'baptism_date'                =>  $memberData->baptismDate,
            'blood_type'                  =>  $memberData->bloodType,
            'education'                   =>  $memberData->education,
        ]);
    }


    /**
     * @param array $filters
     * @param string|null $term
     * @param bool $paginate
     * @return Collection|Model
     * @throws BindingResolutionException
     */
    public function getMembers(array $filters, string | null $term, bool $paginate): Collection | Paginator
    {
        $query = function () use ($filters, $term, $paginate) {

            $q = DB::table(self::TABLE_NAME)
                ->where(function ($q) use($filters, $term){
                    if($term != null)
                        $q->where(self::FULL_NAME_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$term}%");

                    if(count($filters) > 0)
                    {
                        if(Arr::exists($filters, 'memberTypes'))
                        {
                            $memberTypes = explode(',', $filters['memberTypes']);

                            foreach ($memberTypes as $memberType)
                            {
                                if($memberType == 'inactive')
                                    $q->where(self::ACTIVATED_COLUMN, BaseRepository::OPERATORS['EQUALS'], false);
                                else
                                    $q->where(self::MEMBER_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $memberType);
                            }
                        }

                        if(Arr::exists($filters, 'membersGenders'))
                        {
                            $membersGenders = explode(',', $filters['membersGenders']);

                            foreach ($membersGenders as $membersGender)
                                $q->where(self::MEMBER_GENDER_COLUMN, BaseRepository::OPERATORS['EQUALS'], $membersGender);
                        }

                        if(Arr::exists($filters, 'ageGroupMembers'))
                        {
                            $ageGroupMembers = explode(',', $filters['ageGroupMembers']);

                            foreach ($ageGroupMembers as $ageGroupMember)
                            {
                                if($ageGroupMember == 'young')
                                {
                                    $q->whereBetween(
                                        DB::raw("STR_TO_DATE(" . self::BORN_DATE_COLUMN . ", '%d%m%Y')"),
                                        [
                                            now()->subYears(35)->toDateString(),
                                            now()->subYears(18)->toDateString(),
                                        ]
                                    );

                                }

                                if($ageGroupMember == 'teen')
                                {
                                    $q->whereBetween(
                                        DB::raw("STR_TO_DATE(" . self::BORN_DATE_COLUMN . ", '%d%m%Y')"),
                                        [
                                            now()->subYears(18)->toDateString(),
                                            now()->subYears(12)->toDateString(),
                                        ]
                                    );
                                }

                                if($ageGroupMember == 'children')
                                {
                                    $q->whereBetween(
                                        DB::raw("STR_TO_DATE(" . self::BORN_DATE_COLUMN . ", '%d%m%Y')"),
                                        [
                                            now()->subYears(11)->toDateString(),
                                            now()->subYears(0)->toDateString(),
                                        ]
                                    );
                                }
                            }
                        }

                    }
                })
                ->orderBy(self::FULL_NAME_COLUMN);


            if($paginate)
            {
                $result = $q->simplePaginate(self::PAGINATE_NUMBER);

                $result->setCollection(
                    $result->getCollection()->map(fn($item) => MemberData::fromResponse((array) $item))
                );
                return $result;
            }
            else
            {
                $result = $q->get();
                return collect($result)->map(fn($item) => MemberData::fromResponse((array) $item));
            }
        };

        return $this->doQuery($query);
    }


    /**
     * @param string $cpf
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getMembersByMiddleCpf(string $cpf): Model | null
    {
        return $this->getItemByColumn(
            self::MIDDLE_CPF_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            $cpf);
    }


    /**
     * @param string $cpf
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getMembersByCpf(string $cpf): Model | null
    {
        return $this->getItemByColumn(
            self::CPF_COLUMN,
            BaseRepository::OPERATORS['LIKE'],
            $cpf);
    }


    /**
     * @param string $id
     * @return Model|null
     * @throws BindingResolutionException
     * @throws UnknownProperties
     */
    public function getMemberById(string $id): MemberData | null
    {
        $group = $this->model
            ->where(self::ID_COLUMN_JOINED, $id)
            ->first();

        if (!$group) {
            return null;
        }

        $attributes = $group->getAttributes();
        return MemberData::fromResponse($attributes);
    }


    /**
     * @param string $month
     * @param string|null $fields
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getMembersByBornMonth(string $month, string $fields = null): Collection | null
    {
        $arrFields = explode(',', $fields);

        if(($key = array_search('age', $arrFields)) !== false)
        {
            unset($arrFields[$key]);
            $arrFields = array_values($arrFields);
        }

        $query = function () use ($month, $fields, $arrFields) {

            $q = DB::table(self::TABLE_NAME)
                ->where(function ($q) use($month){
                    if($month != null)
                        $q->whereRaw("SUBSTRING(" . self::BORN_DATE_COLUMN . ", 3, 2) = ?", [$month]);
                })
                ->where(self::ACTIVATED_COLUMN, 1);

            if ($fields !== null && count($arrFields) > 0) {
                $q = $q->select($arrFields);
            }

            $q = $q->orderBy(self::FULL_NAME_COLUMN);

            $result = $q->get();
            return collect($result)->map(fn($item) => MemberData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }


    /**
     * @param string $month
     * @param bool $paginate
     * @return Collection|Paginator
     * @throws BindingResolutionException
     */
    public function getTithersByMonth(string $month, bool $paginate = false): Collection | Paginator
    {
        $selectColumns  = array_merge(
            self::DISPLAY_SELECT_COLUMNS,
            EntryRepository::DISPLAY_SUM_AMOUNT_COLUMN,
        );

        $selectColumns = array_map(function ($col) {
            return str_contains($col, 'SUM(')
                ? DB::raw($col)
                : $col;
        }, $selectColumns);

        $query = function () use ($month, $selectColumns, $paginate) {
            $q = DB::table(self::TABLE_NAME)
                ->select($selectColumns)
                ->leftJoin(
                    EntryRepository::TABLE_NAME,
                    EntryRepository::MEMBER_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    self::TABLE_NAME . '.' . self::ID_COLUMN
                )
                ->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED, EntryRepository::TITHE_VALUE)
                ->where(EntryRepository::DELETED_COLUMN_JOINED, false)
                ->where(function ($q) use ($month) {
                    $q->where(
                        EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN,
                        BaseRepository::OPERATORS['LIKE'],
                        "%{$month}%"
                    );
                });


            $groupByColumns = array_map(function ($column) {
                return trim(explode(' as ', $column)[0]);
            }, self::DISPLAY_SELECT_COLUMNS);

            $q->groupBy(...$groupByColumns);
            $q = $q->orderBy(self::FULL_NAME_COLUMN);

            if($paginate)
            {
                $result = $q->simplePaginate(self::PAGINATE_NUMBER);

                $result->setCollection(
                    $result->getCollection()->map(fn($item) => MemberData::fromResponse((array) $item))
                );
                return $result;
            }
            else
            {
                $result = $q->get();
                return collect($result)->map(fn($item) => MemberData::fromResponse((array) $item));
            }
        };

        return $this->doQuery($query);
    }



    /**
     * @param int $groupId
     * @param bool $groupLeader
     * @return Collection|Model
     * @throws BindingResolutionException
     */
    public function getMemberAsGroupLeader(int $groupId, bool $groupLeader = true): Collection|Member
    {
        $conditions = [
            [
                'field' => self::ECCLESIASTICAL_DIVISIONS_GROUP_ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $groupId
            ],
            [
                'field' => self::GROUP_LEADER_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => true
            ],
        ];

        return $this->getItemByWhere(
            ['*'],
            $conditions
        );
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
     * @param int $memberId
     * @param string $middleCpf
     * @return bool
     * @throws BindingResolutionException
     */
    public function updateMiddleCpf(int $memberId, string $middleCpf): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $memberId
        ];

        return $this->update($conditions, [self::MIDDLE_CPF_COLUMN =>  $middleCpf]);
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
            //'ministries'                =>  $memberData->ministries,
            'baptism_date'              =>  $memberData->baptismDate,
            'blood_type'                =>  $memberData->bloodType,
            'education'                 =>  $memberData->education,
        ]);
    }
}
