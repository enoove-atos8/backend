<?php

namespace Domain\Users\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Mockery\Exception;
use Throwable;

class UploadUserAvatarAction
{
    const USER_AVATAR_PATH = '/assets/users/avatars';
    const TENANT = '/dev';


    public function __construct()
    {

    }

    /**
     * @throws Throwable
     */
    public function __invoke(mixed $image)
    {
        $imageExtension = explode('.', $image->getClientOriginalName())[1];
        $s3 = Storage::disk('s3');
        $imageName = uniqid().'.'.$imageExtension;
        $s3Path = 'clients' . self::TENANT . self::USER_AVATAR_PATH . '/' . $imageName;

        $s3->put($s3Path, file_get_contents($image));

        return $s3->url($s3Path);
    }
}
