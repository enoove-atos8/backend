<?php

namespace Application\Core\Providers;

use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Users\Interfaces\UserDetailRepositoryInterface;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Repositories\Entries\EntryRepository;
use Infrastructure\Repositories\Member\MemberRepository;
use Infrastructure\Repositories\User\UserDetailRepository;
use Infrastructure\Repositories\User\UserRepository;
use Infrastructure\Interfaces\BaseRepositoryInterface;
use Infrastructure\Repositories\BaseRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserDetailRepositoryInterface::class, UserDetailRepository::class);
        $this->app->bind(ChurchRepositoryInterface::class, ChurchRepository::class);
        $this->app->bind(EntryRepositoryInterface::class, EntryRepository::class);
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
