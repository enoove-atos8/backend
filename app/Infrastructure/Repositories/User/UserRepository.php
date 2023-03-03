<?php

namespace Infrastructure\Repositories\User;

use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Domain\Users\SubDomains\Roles\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function createUser(UserData $userData): User
    {
        return $this->model->create([
            'email'     =>  $userData->email,
            'password'  =>  bcrypt($userData->password),
            'type'      =>  $userData->type,
        ]);
    }

    public function attachRoles(UserData $userData, User $user): void
    {
        $user->roles()->attach($userData->roles['role_id']);
    }

    public function attachAbilities(UserData $userData, User $user): void
    {
        $role = $user->roles()->first();

        foreach ($userData->roles['abilities'] as $ability){
            $role->abilities()->attach($ability['id']);
        }
    }
}
