<?php

namespace Application\Core\Providers;

use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Domain\Entries\General\Interfaces\EntryRepositoryInterface;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Users\Interfaces\UserDetailRepositoryInterface;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Interfaces\BaseRepositoryInterface;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Repositories\Entries\Consolidated\ConsolidatedEntriesRepository;
use Infrastructure\Repositories\Entries\General\EntryRepository;
use Infrastructure\Repositories\Member\MemberRepository;
use Infrastructure\Repositories\User\UserDetailRepository;
use Infrastructure\Repositories\User\UserRepository;

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
        $this->app->bind(ConsolidatedEntriesRepositoryInterface::class, ConsolidatedEntriesRepository::class);
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
