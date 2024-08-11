<?php

namespace Application\Core\Console\Commands;

use Illuminate\Console\Command;

class AppMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations in a specific order';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $migrations =
            [
            '0001_2014_10_12_000000_create_users_table.php',
            '0002_2014_10_12_100000_create_password_resets_table.php',
            '0003_2019_08_19_000000_create_failed_jobs_table.php',
            '0004_2019_12_14_000001_create_personal_access_tokens_table.php',
            '0004_2024_03_12_183600_create_financial_reviewers_table.php',
            '0005_2023_12_06_212600_create_members_table.php',
            '0006_2023_09_06_000000_create_entries_table.php',
            '0007_2024_01_13_000000_create_consolidation_entries_table.php',
            '0008_2023_09_24_000000_create_user_details_table.php',
            '0009_2024_02_27_000000_create_financial_settings_table.php',
            '2024_03_31_105500_create_menu_table.php',
            '2024_07_29_152234_create_permission_tables.php',
            '2024_08_10_123832_2024_08_10_create_ministries_tables.php',
            '2024_08_19_160000_create_menu_role_table.php',
        ];

        foreach ($migrations as $migration)
        {
            $this->call('tenants:migrate', ['--path' => 'database/migrations/tenant/' . $migration]);
        }
    }
}
