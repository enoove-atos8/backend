<?php

namespace Application\Core\Providers;

use Domain\Employees\Interfaces\EmployeeRepositoryInterface;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Infrastructure\Repositories\Employee\EmployeeRepository;
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
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
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
