<?php

namespace App\Domain\Accounts\TenantVerification\Interfaces;



use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Accounts\Users\Models\UserDetail;

interface UserDetailRepositoryInterface
{
    public function createUserDetail($userId, UserDetailData $userDetailData): UserDetail;

    public function updateUserDetail($id, UserDetailData $userDetailData): int;
}
