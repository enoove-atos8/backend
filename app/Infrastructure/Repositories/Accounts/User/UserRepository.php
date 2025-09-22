<?php

namespace App\Infrastructure\Repositories\Accounts\User;

use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Domain\Accounts\Users\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected mixed $model = User::class;

    const TABLE_NAME = 'users';
    const DATE_ENTRY_REGISTER_COLUMN = 'date_entry_register';
    const DELETED_COLUMN = 'deleted';
    const ENTRY_TYPE_COLUMN = 'entry_type';
    const AMOUNT_COLUMN = 'amount';
    const DEVOLUTION_COLUMN = 'devolution';
    const ID_COLUMN = 'id';
    const ID_COLUMN_JOINED = 'users.id';

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
     * @param UserData $userData
     * @return User
     */
    public function createUser(UserData $userData): User
    {
        return $this->create([
            'email'                 =>  strtolower($userData->email),
            'password'              =>  bcrypt($userData->password),
            'activated'             =>  $userData->activated,
            'type'                  =>  $userData->type,
            'changed_password'      =>  $userData->changedPassword,
            'access_quantity'       =>  $userData->accessQuantity,
        ]);
    }


    /**
     * @param null $id
     * @return Collection|Model
     * @throws BindingResolutionException
     */
    public function getUsers($id = null): Collection|User
    {
        $this->requiredRelationships = ['detail'];

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
     * @param UserData $userData
     * @return int
     * @throws BindingResolutionException
     */
    public function updateUser($id, UserData $userData): int
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id
        ];
        return $this->update($conditions, [
            'email'                 =>  $userData->email,
            'activated'             =>  $userData->activated,
            'type'                  =>  $userData->type,
            'changed_password'      =>  $userData->changedPassword,
            'access_quantity'       =>  $userData->accessQuantity,
        ]);
    }
}
