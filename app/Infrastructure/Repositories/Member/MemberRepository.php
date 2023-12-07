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
            //'email'                 =>  strtolower($userData->email),
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
            return $this->getAll();
    }


    /**
     * @param null $id
     * @param $status
     * @return int
     * @throws BindingResolutionException
     */
    public function updateStatus($id, $status): int
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
     * @return int
     * @throws BindingResolutionException
     */
    public function updateMember($id, MemberData $memberData): int
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id
        ];
        return $this->update($conditions, [
            'email'                 =>  $memberData->email,
        ]);
    }
}
