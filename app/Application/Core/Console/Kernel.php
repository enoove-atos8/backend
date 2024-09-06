<?php

namespace Application\Core\Console;

use Application\Core\Console\Commands\AppMigrate;
use Application\Core\Jobs\ProcessGoogleDriveFilesJob;
use Google\Service\Exception;
use Illuminate\Console\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AppMigrate::class
    ];


    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            (new ProcessGoogleDriveFilesJob())->handle();
        })->dailyAt('11:16');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
