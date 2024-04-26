<?php

namespace App\Domain\Notifications\Actions\Church;


use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Notifications\Mail\Church\NewChurchUserMail;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NewChurchUserNotificationAction
{

    public function __construct(
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(UserData $userData, UserDetailData $userDetailData, string $tenant): void
    {
        Mail::to($userData->email)->send(new NewChurchUserMail($userData, $userDetailData, $tenant));
    }
}
