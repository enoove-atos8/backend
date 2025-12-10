<?php

namespace App\Domain\Accounts\Users\Actions;

use App\Domain\Accounts\Users\Interfaces\UserDetailRepositoryInterface;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserDetailRepositoryInterface $userDetailRepository
    ) {}

    /**
     * Exclui um usuário e seus detalhes (hard delete)
     *
     * @throws Throwable
     */
    public function execute(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            // Primeiro exclui os detalhes do usuário
            $this->userDetailRepository->deleteUserDetail($id);

            // Depois exclui o usuário
            return $this->userRepository->deleteUser($id);
        });
    }
}
