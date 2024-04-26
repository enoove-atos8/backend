<?php

namespace App\Infrastructure\Repositories\Accounts\User;

use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Accounts\Users\Interfaces\UserDetailRepositoryInterface;
use App\Domain\Accounts\Users\Models\UserDetail;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\BaseRepository;

class UserDetailRepository extends BaseRepository implements UserDetailRepositoryInterface
{
    protected mixed $model = UserDetail::class;

    const USER_ID_COLUMN = 'user_id';


    /**
     * @param $userId
     * @param UserDetailData $userDetailData
     * @return UserDetail
     */
    public function createUserDetail($userId, UserDetailData $userDetailData): UserDetail
    {
        return $this->create([
            'user_id'      =>  $userId,
            'full_name'    =>  ucwords(strtolower($userDetailData->full_name)),
            'avatar'       =>  $userDetailData->avatar,
            'type'         =>  $userDetailData->type,
            'title'        =>  $userDetailData->title,
            'gender'       =>  $userDetailData->gender,
            'phone'        =>  $userDetailData->phone,
            'address'      =>  ucwords(strtolower($userDetailData->address)),
            'district'     =>  $userDetailData->district,
            'city'         =>  $userDetailData->city,
            'country'      =>  $userDetailData->country,
            'birthday'     =>  $userDetailData->birthday,
        ]);
    }


    /**
     * @param $id
     * @param UserDetailData $userDetailData
     * @return int
     * @throws BindingResolutionException
     */
    public function updateUserDetail($id, UserDetailData $userDetailData): int
    {
        $conditions = [
            'field' => self::USER_ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id
        ];
        return $this->update($conditions, [
            'full_name'    =>  ucwords(strtolower($userDetailData->full_name)),
            'avatar'       =>  $userDetailData->avatar,
            'type'         =>  $userDetailData->type,
            'title'        =>  $userDetailData->title,
            'gender'       =>  $userDetailData->gender,
            'phone'        =>  $userDetailData->phone,
            'address'      =>  ucwords(strtolower($userDetailData->address)),
            'district'     =>  $userDetailData->district,
            'city'         =>  $userDetailData->city,
            'country'      =>  $userDetailData->country,
            'birthday'     =>  $userDetailData->birthday,
        ]);
    }
}
