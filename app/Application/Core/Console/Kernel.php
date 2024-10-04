<?php

namespace Application\Core\Console;

use App\Application\Core\Jobs\ProcessingEntriesByCollectionWorship;
use Application\Core\Jobs\ProcessingEntriesByBankTransfer;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        /*$schedule->call(function () {
            resolve(ProcessingEntriesByBankTransfer::class)->handle();
        })->dailyAt('11:36');*/

        $schedule->call(function () {
            resolve(ProcessingEntriesByCollectionWorship::class)->handle();
        })->dailyAt('17:40');
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
