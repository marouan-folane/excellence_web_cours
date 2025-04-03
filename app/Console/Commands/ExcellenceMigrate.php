<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ExcellenceMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excellence:migrate {--fresh : Wipe the database before running migrations} {--seed : Seed the database with initial data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all migrations to set up the Excellence database schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Excellence database migration process...');

        // Determine if we should use fresh migrations
        $fresh = $this->option('fresh');
        $seed = $this->option('seed');

        // Build the migration command
        $command = $fresh ? 'migrate:fresh' : 'migrate';
        
        // Add seed option if required
        $options = $seed ? ['--seed' => true] : [];
        
        // Add step option to ensure migrations run in order
        $options['--step'] = true;
        
        // Run the migration command
        $this->info('Running ' . $command . '...');
        $exitCode = Artisan::call($command, $options);
        
        // Display the output from the migration command
        $this->info(Artisan::output());

        if ($exitCode === 0) {
            // Now run DB functions migration
            $this->info('Setting up database functions...');
            $exitCode = Artisan::call('migrate', ['--path' => 'database/migrations/2025_04_04_000002_add_custom_db_functions.php']);
            $this->info(Artisan::output());
            
            if ($exitCode === 0) {
                $this->info('Excellence database setup completed successfully!');
            } else {
                $this->error('Failed to set up database functions.');
            }
        } else {
            $this->error('Migration process failed with exit code: ' . $exitCode);
        }

        return $exitCode;
    }
} 