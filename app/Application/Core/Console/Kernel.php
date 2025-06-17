<?php

namespace Application\Core\Console;

use App\Application\Core\Jobs\Financial\Entries\ReceiptsProcessing\ProcessingBankEntriesTransferReceipts;
use App\Application\Core\Jobs\Financial\Exits\ReceiptsProcessing\ProcessingBankExitsTransferReceipts;
use Application\Core\Jobs\Financial\Entries\Reports\HandlerEntriesReports;
use Application\Core\Jobs\Financial\Purchases\ProcessingInvoicesStatus;
use Application\Core\Jobs\Financial\Purchases\ProcessingPurchaseCards;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Infrastructure\Exceptions\GeneralExceptions;

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
            try{
                resolve(ProcessingBankEntriesTransferReceipts::class)->handle();
            }catch (\Throwable $e){
                throw new GeneralExceptions('Erro na execução do agendamento ProcessingBankEntriesTransferReceipts', 500, $e);
            }
        })->everyFifteenMinutes();

        // Exits
        $schedule->call(function () {
            try{
                resolve(ProcessingBankExitsTransferReceipts::class)->handle();
            }catch (\Throwable $e){
                throw new GeneralExceptions('Erro na execução do agendamento ProcessingBankExitsTransferReceipts', 500, $e);
            }
        })->everyFifteenMinutes();

        // Reports
        $schedule->call(function () {
            try{
                resolve(HandlerEntriesReports::class)->handle();
            }catch (\Throwable $e){
                throw new GeneralExceptions('Erro na execução do agendamento HandlerEntriesReports', 500, $e);
            }
        })->everyMinute();

        // Purchases
        $schedule->call(function () {
            try{
                resolve(ProcessingPurchaseCards::class)->handle();
            }catch (\Throwable $e){
                throw new GeneralExceptions('Erro na execução do agendamento ProcessingPurchaseCards', 500, $e);
            }
        })->everyFifteenMinutes();

        // Update invoice closing
        $schedule->call(function () {
            try{
                resolve(ProcessingInvoicesStatus::class)->handle();
            }catch (\Throwable $e){
                throw new GeneralExceptions('Erro na execução do agendamento ProcessingInvoicesStatus', 500, $e);
            }
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
