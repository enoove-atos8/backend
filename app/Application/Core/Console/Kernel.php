<?php

namespace Application\Core\Console;

use App\Application\Core\Jobs\Financial\Entries\ReceiptsProcessing\ProcessingBankEntriesTransferReceipts;
use App\Application\Core\Jobs\Financial\Exits\ReceiptsProcessing\ProcessingBankExitsTransferReceipts;
use Application\Core\Jobs\Financial\Entries\Reports\HandlerEntriesReports;
use Application\Core\Jobs\Financial\Purchases\ProcessingInvoicesClosing;
use Application\Core\Jobs\Financial\Purchases\ProcessingPurchaseCards;
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
        })->everyFifteenMinutes();

        // Exits
        $schedule->call(function () {
            resolve(ProcessingBankExitsTransferReceipts::class)->handle();
        })->everyFifteenMinutes();

        // Reports
        $schedule->call(function () {
            resolve(HandlerEntriesReports::class)->handle();
        })->everyMinute();

        // Purchases
        $schedule->call(function () {
            resolve(ProcessingPurchaseCards::class)->handle();
        })->everyFifteenMinutes();

        // Update invoice closing
        $schedule->call(function () {
            resolve(ProcessingInvoicesClosing::class)->handle();
        })->hourly();
    }



    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        require base_path('routes/console.php');
    }
}
