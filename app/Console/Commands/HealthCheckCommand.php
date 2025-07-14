<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the application is healthy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Check database connection
            DB::connection()->getPdo();

            // Check storage directory is writable
            if (! is_writable(storage_path())) {
                throw new \Exception('Storage directory is not writable');
            }

            // Check if the application key is set
            if (empty(config('app.key'))) {
                throw new \Exception('Application key is not set');
            }

            $this->info('Application is healthy');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Application is not healthy: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
