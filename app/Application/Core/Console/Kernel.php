<?php

namespace Application\Core\Console;

use App\Application\Core\Jobs\Financial\Entries\ReceiptsProcessing\ProcessingBankEntriesTransferReceipts;
use App\Application\Core\Jobs\Financial\Exits\ReceiptsProcessing\ProcessingBankExitsTransferReceipts;
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
            resolve(ProcessingBankEntriesTransferReceipts::class)->handle();
        })->at('19:42');

        // Exits
        /*$schedule->call(function () {
            resolve(ProcessingBankExitsTransferReceipts::class)->handle();
        })->at('16:14');*/

        // Reports
        /*$schedule->call(function () {
            resolve(HandlerEntriesReports::class)->handle();
        })->everyMinute();*/
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
