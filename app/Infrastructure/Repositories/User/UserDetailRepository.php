<?php

namespace Infrastructure\Repositories\User;

use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\DataTransferObjects\UserDetailData;
use Domain\Users\Interfaces\UserDetailRepositoryInterface;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Domain\Users\Models\UserDetail;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class UserDetailRepository extends BaseRepository implements UserDetailRepositoryInterface
{
    protected mixed $model = UserDetail::class;

    public function createUserDetail($userId, UserDetailData $userDetailData): UserDetail
    {
        return $this->create([
            'user_id'      =>  $userId,
            'full_name'    =>  $userDetailData->full_name,
            'avatar'       =>  $userDetailData->avatar,
            'type'         =>  $userDetailData->type,
            'title'        =>  $userDetailData->title,
            'phone'        =>  $userDetailData->phone,
            'address'      =>  $userDetailData->address,
            'district'     =>  $userDetailData->district,
            'city'         =>  $userDetailData->city,
            'country'      =>  $userDetailData->country,
            'birthday'     =>  $userDetailData->birthday,
        ]);
    }
}
