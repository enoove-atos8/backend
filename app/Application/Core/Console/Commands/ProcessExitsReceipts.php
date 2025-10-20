<?php

namespace Application\Core\Console\Commands;

use App\Application\Core\Jobs\Financial\Exits\ReceiptsProcessing\ProcessingBankExitsTransferReceipts;
use Illuminate\Console\Command;
use Infrastructure\Exceptions\GeneralExceptions;

class ProcessExitsReceipts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exits:process-receipts';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process bank exits transfer receipts from sync_storage';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting exits receipts processing...');

        try {
            resolve(ProcessingBankExitsTransferReceipts::class)->handle();
            $this->info('Exits receipts processing completed successfully!');
        } catch (GeneralExceptions $e) {
            $this->error('Error processing exits receipts: ' . $e->getMessage());
        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
        }
    }
}
