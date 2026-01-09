<?php

namespace Application\Core\Providers;

use App\Domain\Accounts\Users\Models\User;
use Application\Core\Observers\UserObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('pt_BR');

        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // Register User Observer for global table sync
        User::observe(UserObserver::class);
    }
}
