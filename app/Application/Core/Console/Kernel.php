<?php

namespace Application\Core\Console;

use Application\Core\Jobs\Financial\Entries\Automation\ReceiptsProcessing\ProcessingEntriesByBankTransfer;
use Application\Core\Jobs\Financial\Entries\Reports\HandlerEntriesReports;
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
        // =============================================================
        // @ Financial
        // =============================================================

        // Entries
        $schedule->call(function () {
            resolve(ProcessingEntriesByBankTransfer::class)->handle();
        })->at('14:23');


        // Reports
        //$schedule->call(function () {
        //    resolve(HandlerEntriesReports::class)->handle();
        //})->everyMinute();
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
