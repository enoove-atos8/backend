<?php

namespace App\Infrastructure\Repositories\Users\User;

use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Accounts\Users\Interfaces\UserDetailRepositoryInterface;
use App\Domain\Accounts\Users\Models\UserDetail;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\BaseRepository;

class UserDetailRepository extends BaseRepository implements UserDetailRepositoryInterface
{
    protected mixed $model = UserDetail::class;

    const TABLE_NAME = 'user_details';
    const USER_ID_COLUMN = 'user_id';

    const DISPLAY_SELECT_COLUMNS = [
        'user_details.user_id as user_detail_user_id',
        'user_details.full_name as user_detail_full_name',
        'user_details.avatar as user_detail_avatar',
        'user_details.type as user_detail_type',
        'user_details.title as user_detail_title',
        'user_details.gender as user_detail_gender',
        'user_details.phone as user_detail_phone',
        'user_details.address as user_detail_address',
        'user_details.district as user_detail_district',
        'user_details.city as user_detail_city',
        'user_details.country as user_detail_country',
        'user_details.birthday as user_detail_birthday',
    ];


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
