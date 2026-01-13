<?php

namespace App\Infrastructure\Repositories\Secretary\Membership;

use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Domain\Secretary\Membership\Models\Member;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MemberRepository extends BaseRepository implements MemberRepositoryInterface
{
    protected mixed $model = Member::class;

    const TABLE_NAME = 'members';

    const MEMBER_VALUE = 'member';

    const CONGREGATE_VALUE = 'congregate';

    const ID_COLUMN_JOINED = 'members.id';

    const PRINCIPAL_MEMBER_ID_COLUMN = 'principal_member_id';

    const MEMBER_TYPE_COLUMN_JOINED = 'members.member_type';

    const MEMBER_TYPE_COLUMN = 'member_type';

    const PAGINATE_NUMBER = 30;

    const DELETED_COLUMN = 'deleted';

    const MEMBER_GENDER_COLUMN_JOINED = 'members.gender';

    const MEMBER_GENDER_COLUMN = 'gender';

    const ACTIVATED_COLUMN = 'activated';

    const MIDDLE_CPF_COLUMN = 'middle_cpf';

    const CPF_COLUMN = 'cpf';

    const ALL_COLUMNS = '*';

    const FULL_NAME_COLUMN = 'full_name';

    const DEACTIVATION_REASON_COLUMN = 'deactivation_reason';

    const BORN_DATE_COLUMN = 'born_date';

    const ECCLESIASTICAL_DIVISIONS_GROUP_ID_COLUMN = 'ecclesiastical_divisions_group_id';

    const GROUP_LEADER_COLUMN = 'group_leader';

    const GROUP_LEADER_COLUMN_JOINED = 'members.group_leader';

    const GROUP_LEADER_COLUMN_ALIAS = 'members_group_leader';

    const CHILDREN_VALUE = 'children';

    const YOUNG_VALUE = 'young';

    const TEEN_VALUE = 'teen';

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
        'members.education as members_education',
        'members.group_ids as members_group_ids',
        'members.dependents_members_ids as members_dependents_members_ids',
        'members.group_leader as members_group_leader',
    ];

    /**
     * Array of where, between and another clauses that was mounted dynamically
     */
    private array $queryClausesAndConditions = [
        'where_clause' => [
            'exists' => false,
            'clause' => [],
        ],
    ];

    public function createMember(MemberData $memberData): Member
    {
        return $this->create([
            'activated' => $memberData->activated,
            'deleted' => $memberData->deleted,
            'avatar' => $memberData->avatar,
            'full_name' => $memberData->fullName,
            'gender' => $memberData->gender,
            'cpf' => $memberData->cpf,
            'rg' => $memberData->rg,
            'work' => $memberData->work,
            'born_date' => $memberData->bornDate,
            'email' => strtolower($memberData->email),
            'phone' => $memberData->phone,
            'cell_phone' => $memberData->cellPhone,
            'address' => $memberData->address,
            'district' => $memberData->district,
            'city' => $memberData->city,
            'uf' => $memberData->uf,
            'marital_status' => $memberData->maritalStatus,
            'spouse' => $memberData->spouse,
            'father' => $memberData->father,
            'mother' => $memberData->mother,
            // 'ecclesiastical_function'     =>  $memberData->ecclesiasticalFunction,
            'member_type' => $memberData->memberType,
            'baptism_date' => $memberData->baptismDate,
            'blood_type' => $memberData->bloodType,
            'education' => $memberData->education,
            'group_ids' => $memberData->groupIds,
            'dependents_members_ids' => $memberData->dependentsMembersIds ? json_encode($memberData->dependentsMembersIds) : null,
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getMembers(array $filters, ?string $term, bool $paginate): array
    {
        $query = function () use ($filters, $term, $paginate) {

            // Verifica se o filtro de inativos foi solicitado
            $showInactive = false;
            if (Arr::exists($filters, 'memberTypes')) {
                $memberTypes = explode(',', $filters['memberTypes']);
                $showInactive = in_array('inactive', $memberTypes);
            }

            $q = DB::table(self::TABLE_NAME)
                ->when(! $showInactive, function ($query) {
                    return $query->where(self::ACTIVATED_COLUMN, true);
                })
                ->when($showInactive, function ($query) {
                    return $query->where(self::ACTIVATED_COLUMN, false);
                })
                ->where(function ($q) use ($filters, $term) {
                    if ($term != null) {
                        $q->where(self::FULL_NAME_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$term}%");
                    }

                    if (count($filters) > 0) {
                        if (Arr::exists($filters, 'memberTypes')) {
                            $memberTypes = explode(',', $filters['memberTypes']);

                            foreach ($memberTypes as $memberType) {
                                // Ignora 'inactive' pois já foi tratado acima
                                if ($memberType != 'inactive') {
                                    $q->where(self::MEMBER_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $memberType);
                                }
                            }
                        }

                        if (Arr::exists($filters, 'membersGenders')) {
                            $membersGenders = explode(',', $filters['membersGenders']);

                            foreach ($membersGenders as $membersGender) {
                                $q->where(self::MEMBER_GENDER_COLUMN, BaseRepository::OPERATORS['EQUALS'], $membersGender);
                            }
                        }

                        if (Arr::exists($filters, 'ageGroupMembers')) {
                            $ageGroupMembers = explode(',', $filters['ageGroupMembers']);

                            foreach ($ageGroupMembers as $ageGroupMember) {
                                if ($ageGroupMember == self::YOUNG_VALUE) {
                                    $q->whereBetween(
                                        DB::raw('STR_TO_DATE('.self::BORN_DATE_COLUMN.", '%d%m%Y')"),
                                        [
                                            now()->subYears(35)->toDateString(),
                                            now()->subYears(18)->toDateString(),
                                        ]
                                    );

                                }

                                if ($ageGroupMember == self::TEEN_VALUE) {
                                    $q->whereBetween(
                                        DB::raw('STR_TO_DATE('.self::BORN_DATE_COLUMN.", '%d%m%Y')"),
                                        [
                                            now()->subYears(17)->toDateString(),
                                            now()->subYears(12)->toDateString(),
                                        ]
                                    );
                                }

                                if ($ageGroupMember == self::CHILDREN_VALUE) {
                                    $q->whereBetween(
                                        DB::raw('STR_TO_DATE('.self::BORN_DATE_COLUMN.", '%d%m%Y')"),
                                        [
                                            now()->subYears(11)->toDateString(),
                                            now()->subYears(0)->toDateString(),
                                        ]
                                    );
                                }
                            }
                        }

                        if (Arr::exists($filters, 'ageFrom') && Arr::exists($filters, 'ageTo')) {
                            $ageFrom = $filters['ageFrom'];
                            $ageTo = $filters['ageTo'];

                            $q->whereBetween(
                                DB::raw('STR_TO_DATE('.self::BORN_DATE_COLUMN.", '%d%m%Y')"),
                                [
                                    now()->subYears($ageTo)->toDateString(),
                                    now()->subYears($ageFrom)->toDateString(),
                                ]
                            );
                        } elseif (Arr::exists($filters, 'ageFrom')) {
                            $ageFrom = $filters['ageFrom'];
                            $q->where(
                                DB::raw('STR_TO_DATE('.self::BORN_DATE_COLUMN.", '%d%m%Y')"),
                                '<=', now()->subYears($ageFrom)->toDateString()
                            );
                        } elseif (Arr::exists($filters, 'ageTo')) {
                            $ageTo = $filters['ageTo'];
                            $q->where(
                                DB::raw('STR_TO_DATE('.self::BORN_DATE_COLUMN.", '%d%m%Y')"),
                                '>=', now()->subYears($ageTo)->toDateString()
                            );
                        }
                    }
                })
                ->orderBy(self::FULL_NAME_COLUMN);

            if ($paginate) {
                $countRows = count($q->get());
                $result = $q->simplePaginate(self::PAGINATE_NUMBER);

                $membersWithDependents = $this->populateDependentsMembersCollection($result->getCollection());
                $result->setCollection(collect($membersWithDependents));

                return [
                    'results' => $result,
                    'countRows' => $countRows,
                ];

            } else {
                $countRows = count($q->get());
                $result = $q->get();
                $results = $this->populateDependentsMembersCollection($result);

                return [
                    'results' => collect($results),
                    'countRows' => $countRows,
                ];
            }
        };

        return $this->doQuery($query);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getMembersByMiddleCpf(string $cpf): ?Model
    {
        return $this->getItemByColumn(
            self::MIDDLE_CPF_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            $cpf);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getMembersByCpf(string $cpf): ?Model
    {
        return $this->getItemByColumn(
            self::CPF_COLUMN,
            BaseRepository::OPERATORS['LIKE'],
            $cpf);
    }

    /**
     * @return Model|null
     *
     * @throws BindingResolutionException
     * @throws UnknownProperties
     */
    public function getMemberById(string $id): ?MemberData
    {
        $group = $this->model
            ->where(self::ID_COLUMN_JOINED, $id)
            ->first();

        if (! $group) {
            return null;
        }

        $attributes = $group->getAttributes();
        $memberData = MemberData::fromResponse($attributes);

        return $this->populateDependentsMembers($memberData);
    }

    /**
     * Popula os dados completos dos membros dependentes para um único membro
     */
    private function populateDependentsMembers(MemberData $memberData): MemberData
    {
        if (! empty($memberData->dependentsMembersIds)) {
            $dependentsMembers = DB::table(self::TABLE_NAME)
                ->whereIn(self::ID_COLUMN, $memberData->dependentsMembersIds)
                ->get([self::ID_COLUMN, self::FULL_NAME_COLUMN, 'avatar'])
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'fullName' => $item->full_name,
                    'avatar' => $item->avatar,
                ])
                ->toArray();

            $memberData->dependentsMembers = $dependentsMembers;
        }

        return $memberData;
    }

    /**
     * Popula os dados completos dos membros dependentes para uma coleção de membros
     * Otimizado para evitar N+1 queries
     */
    private function populateDependentsMembersCollection(iterable $members): array
    {
        $membersArray = collect($members)->map(fn ($item) => MemberData::fromResponse((array) $item))->toArray();

        // Coletar todos os IDs de dependentes únicos
        $allDependentIds = [];
        foreach ($membersArray as $member) {
            if (! empty($member->dependentsMembersIds)) {
                $allDependentIds = array_merge($allDependentIds, $member->dependentsMembersIds);
            }
        }
        $allDependentIds = array_unique($allDependentIds);

        // Buscar todos os dependentes em uma única query
        $dependentsData = [];
        if (! empty($allDependentIds)) {
            $dependentsData = DB::table(self::TABLE_NAME)
                ->whereIn(self::ID_COLUMN, $allDependentIds)
                ->get([self::ID_COLUMN, self::FULL_NAME_COLUMN, 'avatar'])
                ->keyBy(self::ID_COLUMN)
                ->toArray();
        }

        // Mapear os dependentes para cada membro
        foreach ($membersArray as $member) {
            if (! empty($member->dependentsMembersIds)) {
                $dependentsMembers = [];
                foreach ($member->dependentsMembersIds as $dependentId) {
                    if (isset($dependentsData[$dependentId])) {
                        $dependent = $dependentsData[$dependentId];
                        $dependentsMembers[] = [
                            'id' => $dependent->id,
                            'fullName' => $dependent->full_name,
                            'avatar' => $dependent->avatar,
                        ];
                    }
                }
                $member->dependentsMembers = $dependentsMembers;
            }
        }

        return $membersArray;
    }

    /**
     * @return Model|null
     *
     * @throws BindingResolutionException
     */
    public function getMembersByBornMonth(string $month, ?string $fields = null): ?Collection
    {
        $arrFields = explode(',', $fields);

        if (($key = array_search('age', $arrFields)) !== false) {
            unset($arrFields[$key]);
            $arrFields = array_values($arrFields);
        }

        $query = function () use ($month, $fields, $arrFields) {

            $q = DB::table(self::TABLE_NAME)
                ->where(function ($q) use ($month) {
                    if ($month != null) {
                        $q->whereRaw('SUBSTRING('.self::BORN_DATE_COLUMN.', 3, 2) = ?', [$month]);
                    }
                })
                ->where(self::ACTIVATED_COLUMN, 1);

            if ($fields !== null && count($arrFields) > 0) {
                $q = $q->select($arrFields);
            }

            $q = $q->orderByRaw('SUBSTRING('.self::BORN_DATE_COLUMN.', 1, 2)');

            $result = $q->get();

            return collect($result)->map(fn ($item) => MemberData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getTithersByMonth(string $month, bool $paginate = false): Collection|Paginator
    {
        // Remove o campo group_leader original do DISPLAY_SELECT_COLUMNS
        $displayColumns = array_filter(self::DISPLAY_SELECT_COLUMNS, function ($col) {
            return ! str_contains($col, self::GROUP_LEADER_COLUMN_JOINED);
        });

        // Subquery para verificar se o membro é líder de algum grupo
        $isLeaderSubquery = '(EXISTS (SELECT 1 FROM '.GroupsRepository::TABLE_NAME.
            ' WHERE '.GroupsRepository::LEADER_ID_COLUMN.' = '.self::ID_COLUMN_JOINED.
            ' AND '.GroupsRepository::DELETED_COLUMN.' = 0)) as '.self::GROUP_LEADER_COLUMN_ALIAS;

        $selectColumns = array_merge(
            $displayColumns,
            EntryRepository::DISPLAY_SUM_AMOUNT_COLUMN,
            [$isLeaderSubquery],
        );

        $selectColumns = array_map(function ($col) {
            return str_contains($col, 'SUM(') || str_contains($col, 'EXISTS')
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
                    self::TABLE_NAME.'.'.self::ID_COLUMN
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

            $groupByColumns = array_filter(
                array_map(function ($column) {
                    return trim(explode(' as ', $column)[0]);
                }, self::DISPLAY_SELECT_COLUMNS),
                function ($col) {
                    return $col !== self::GROUP_LEADER_COLUMN_JOINED;
                }
            );

            $q->groupBy(...$groupByColumns);
            $q = $q->orderBy(self::FULL_NAME_COLUMN);

            if ($paginate) {
                $result = $q->simplePaginate(self::PAGINATE_NUMBER);

                $result->setCollection(
                    $result->getCollection()->map(fn ($item) => MemberData::fromResponse((array) $item))
                );

                return $result;
            } else {
                $result = $q->get();

                return collect($result)->map(fn ($item) => MemberData::fromResponse((array) $item));
            }
        };

        return $this->doQuery($query);
    }

    /**
     * @return Collection|Model
     *
     * @throws BindingResolutionException
     */
    public function getMemberAsGroupLeader(int $groupId, bool $groupLeader = true): Collection|Member
    {
        $conditions = [
            [
                'field' => self::ECCLESIASTICAL_DIVISIONS_GROUP_ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $groupId,
            ],
            [
                'field' => self::GROUP_LEADER_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => true,
            ],
        ];

        return $this->getItemByWhere(
            ['*'],
            $conditions
        );
    }

    /**
     * @throws BindingResolutionException
     */
    public function getMembersByGroupId(int $groupId): ?Collection
    {
        $query = function () use ($groupId) {

            $q = DB::table(self::TABLE_NAME)
                ->whereRaw('JSON_CONTAINS(group_ids, ?, "$")', [(string) $groupId])
                ->where(self::ACTIVATED_COLUMN, 1)
                ->orderBy(self::FULL_NAME_COLUMN);

            $result = $q->get();

            return collect($result)->map(fn ($item) => MemberData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }

    /**
     * @param  null  $id
     * @return int
     *
     * @throws BindingResolutionException
     */
    public function updateStatus($id, $status): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,
        ];

        return $this->update($conditions, ['activated' => $status]);
    }

    /**
     * @return bool
     *
     * @throws BindingResolutionException
     */
    public function updateMiddleCpf(int $memberId, string $middleCpf): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $memberId,
        ];

        return $this->update($conditions, [self::MIDDLE_CPF_COLUMN => $middleCpf]);
    }

    /**
     * @param  null  $id
     * @return bool
     *
     * @throws BindingResolutionException
     */
    public function updateMember($id, MemberData $memberData): mixed
    {
        $conditions = ['field' => self::ID_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => $id];

        return $this->update($conditions, [
            'activated' => $memberData->activated,
            'deleted' => $memberData->activated,
            'avatar' => $memberData->avatar,
            'full_name' => $memberData->fullName,
            'gender' => $memberData->gender,
            'cpf' => $memberData->cpf,
            'rg' => $memberData->rg,
            'work' => $memberData->work,
            'born_date' => $memberData->bornDate,
            'email' => strtolower($memberData->email),
            'phone' => $memberData->phone,
            'cell_phone' => $memberData->cellPhone,
            'address' => $memberData->address,
            'district' => $memberData->district,
            'city' => $memberData->city,
            'uf' => $memberData->uf,
            'marital_status' => $memberData->maritalStatus,
            'spouse' => $memberData->spouse,
            'father' => $memberData->father,
            'mother' => $memberData->mother,
            'ecclesiastical_function' => $memberData->ecclesiasticalFunction,
            'member_type' => $memberData->memberType,
            // 'ministries'                =>  $memberData->ministries,
            'baptism_date' => $memberData->baptismDate,
            'blood_type' => $memberData->bloodType,
            'education' => $memberData->education,
            'group_ids' => $memberData->groupIds,
            'dependents_members_ids' => $memberData->dependentsMembersIds ? json_encode($memberData->dependentsMembersIds) : null,
        ]);
    }

    /**
     * Retorna os IDs dos dependentes do membro
     *
     * @throws BindingResolutionException
     */
    public function getDependentsMembersIds(int $memberId): ?array
    {
        $query = function () use ($memberId) {
            $result = DB::table(self::TABLE_NAME)
                ->where(self::ID_COLUMN, $memberId)
                ->first();

            if (! $result) {
                return null;
            }

            $memberData = MemberData::fromResponse((array) $result);

            return $memberData->dependentsMembersIds;
        };

        return $this->doQuery($query);
    }

    /**
     * Retorna o ID do membro principal (dizimista) que tem este membro como dependente
     *
     * @throws BindingResolutionException
     */
    public function getPrincipalMemberId(int $memberId): ?int
    {
        $query = function () use ($memberId) {
            $result = DB::table(self::TABLE_NAME)
                ->whereRaw('JSON_CONTAINS(dependents_members_ids, ?, "$")', [json_encode($memberId)])
                ->first();

            if (! $result) {
                return null;
            }

            $memberData = MemberData::fromResponse((array) $result);

            return $memberData->id;
        };

        return $this->doQuery($query);
    }

    /**
     * @throws BindingResolutionException
     * @throws UnknownProperties
     */
    public function addMembersToGroup(int $groupId, array $memberIds): bool
    {
        try {
            foreach ($memberIds as $memberId) {
                $memberData = $this->getMemberById($memberId);

                if ($memberData) {
                    $groupIds = $memberData->groupIds ?? [];

                    // Adiciona o groupId se não existir
                    if (! in_array($groupId, $groupIds)) {
                        $groupIds[] = $groupId;
                        $memberData->groupIds = $groupIds;

                        // Atualiza o membro
                        $this->updateMember($memberId, $memberData);
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create multiple members in bulk (single INSERT)
     *
     * @param  MemberData[]  $membersData
     */
    public function batchCreateMembers(array $membersData): bool
    {
        $data = array_map(function ($memberData) {
            // Trata group_ids - pode vir como array ou string
            $groupIds = $memberData->groupIds;
            if (is_array($groupIds)) {
                $groupIds = ! empty($groupIds) ? json_encode($groupIds) : null;
            }

            // Trata dependents_members_ids - pode vir como array ou string
            $dependentsMembersIds = $memberData->dependentsMembersIds;
            if (is_array($dependentsMembersIds)) {
                $dependentsMembersIds = ! empty($dependentsMembersIds) ? json_encode($dependentsMembersIds) : null;
            }

            return [
                'activated' => $memberData->activated,
                'deleted' => $memberData->deleted,
                'avatar' => $memberData->avatar,
                'full_name' => $memberData->fullName,
                'gender' => $memberData->gender,
                'cpf' => $memberData->cpf ?: null,
                'rg' => $memberData->rg ?: null,
                'work' => $memberData->work,
                'born_date' => $memberData->bornDate,
                'email' => $memberData->email ? strtolower($memberData->email) : null,
                'phone' => $memberData->phone,
                'cell_phone' => $memberData->cellPhone,
                'address' => $memberData->address,
                'district' => $memberData->district,
                'city' => $memberData->city,
                'uf' => $memberData->uf,
                'marital_status' => $memberData->maritalStatus,
                'spouse' => $memberData->spouse,
                'father' => $memberData->father,
                'mother' => $memberData->mother,
                'member_type' => $memberData->memberType,
                'baptism_date' => $memberData->baptismDate ?: null,
                'blood_type' => $memberData->bloodType,
                'education' => $memberData->education,
                'group_ids' => $groupIds,
                'dependents_members_ids' => $dependentsMembersIds,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $membersData);

        return DB::table(self::TABLE_NAME)->insert($data);
    }

    /**
     * Count active members (activated = true and deleted = false)
     */
    public function countActiveMembers(): int
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ACTIVATED_COLUMN, true)
            ->where(self::DELETED_COLUMN, false)
            ->count();
    }

    /**
     * Update deactivation reason for a member
     *
     * @throws BindingResolutionException
     */
    public function updateDeactivationReason(int $id, ?string $reason): bool
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,
        ];

        return (bool) $this->update($conditions, [self::DEACTIVATION_REASON_COLUMN => $reason]);
    }

    /**
     * Retorna um array com os IDs dos membros que são dependentes
     * [memberId => principalMemberId, ...]
     */
    public function getDependentMembersFromList(array $memberIds): array
    {
        if (empty($memberIds)) {
            return [];
        }

        $query = function () use ($memberIds) {
            $dependents = [];

            // Para cada membro, verifica se ele é dependente de alguém
            foreach ($memberIds as $memberId) {
                $principalId = $this->getPrincipalMemberId($memberId);
                if ($principalId !== null) {
                    $dependents[$memberId] = $principalId;
                }
            }

            return $dependents;
        };

        return $this->doQuery($query);
    }
}
