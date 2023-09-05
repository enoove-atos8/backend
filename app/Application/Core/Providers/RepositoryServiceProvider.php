<?php

namespace Application\Core\Providers;

use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Repositories\Entries\EntryRepository;
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
    public function register()
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ChurchRepositoryInterface::class, ChurchRepository::class);
        $this->app->bind(EntryRepositoryInterface::class, EntryRepository::class);
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
