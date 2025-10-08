<?php

namespace Application\Core\Providers;


use App\Application\Core\Providers\TelescopeServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('pt_BR');

        if (config('app.env') !== 'local')
        {
            URL::forceScheme('https');
        }
    }
}
