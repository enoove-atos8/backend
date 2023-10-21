<?php

namespace Domain\Users\Interfaces;

use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\DataTransferObjects\UserDetailData;
use Domain\Users\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Domain\Users\Models\User;

interface UserDetailRepositoryInterface
{
    public function createUserDetail($userId, UserDetailData $userDetailData): UserDetail;

    public function updateUserDetail($id, UserDetailData $userDetailData): int;
}
