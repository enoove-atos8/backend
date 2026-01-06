<?php

namespace App\Console\Commands;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUsersToGlobalTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync-to-global {--truncate : Truncate global users table before syncing} {--tenant= : Sync only specific tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all tenant users to global users table for faster login tenant discovery';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting user synchronization to global table...');

        // Truncate if requested
        if ($this->option('truncate')) {
            $this->warn('Truncating global users table...');
            DB::connection('mysql')->table('users')->truncate();
            $this->info('Global users table truncated.');
        }

        // Get tenants to sync
        $tenants = $this->option('tenant')
            ? [DB::connection('mysql')->table('tenants')->where('id', $this->option('tenant'))->first()]
            : DB::connection('mysql')->table('tenants')->get();

        if (empty($tenants) || (is_array($tenants) && count($tenants) === 1 && $tenants[0] === null)) {
            $this->error('No tenants found.');

            return Command::FAILURE;
        }

        $totalUsers = 0;
        $bar = $this->output->createProgressBar(count($tenants));
        $bar->start();

        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant->id);

                $users = User::all(['email']);
                $usersCount = $users->count();

                if ($usersCount > 0) {
                    foreach ($users as $user) {
                        DB::connection('mysql')->table('users')->updateOrInsert(
                            [
                                'email' => $user->email,
                                'tenant_id' => $tenant->id,
                            ],
                            [
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        );
                    }

                    $totalUsers += $usersCount;
                }

                tenancy()->end();
            } catch (\Exception $e) {
                $this->error("Failed to sync tenant {$tenant->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Synchronization completed!');
        $this->info("Total users synced: {$totalUsers}");
        $this->info('Total tenants processed: '.count($tenants));

        return Command::SUCCESS;
    }
}
