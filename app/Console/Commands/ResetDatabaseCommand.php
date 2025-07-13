<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ResetDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the database by truncating all tables and re-seeding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->confirm('This will delete all data in the database. Are you sure you want to continue?')) {
            $this->info('Operation cancelled.');

            return;
        }

        $this->info('Resetting database...');

        // Get all tables except migrations
        $tables = $this->getTables();

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate all tables
        foreach ($tables as $table) {
            $this->info("Truncating table: {$table}");
            DB::table($table)->truncate();
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('All tables have been truncated.');

        // Re-seed the database
        $this->info('Re-seeding the database...');
        Artisan::call('db:seed', ['--force' => true]);
        $this->info('Database has been re-seeded.');

        $this->info('Database reset completed successfully!');
    }

    /**
     * Get all tables in the database except migrations.
     *
     * @return array
     */
    protected function getTables()
    {
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$databaseName}";

        return collect($tables)
            ->pluck($tableKey)
            ->filter(function ($table) {
                return $table !== 'migrations';
            })
            ->values()
            ->toArray();
    }
}
