<?php

namespace Domain\Members\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Mockery\Exception;
use Throwable;

class UploadMemberAvatarAction
{
    const MEMBER_AVATAR_PATH = '/assets/members/avatars';


    public function __construct()
    {

    }

    /**
     * @throws Throwable
     */
    public function __invoke(mixed $image, string $tenant)
    {
        $imageExtension = explode('.', $image->getClientOriginalName())[1];
        $s3 = Storage::disk('s3');
        $imageName = uniqid().'.'.$imageExtension;
        $s3Path = 'clients/' . $tenant . self::MEMBER_AVATAR_PATH . '/' . $imageName;

        $s3->put($s3Path, file_get_contents($image));

        return $s3->url($s3Path);
    }
}
