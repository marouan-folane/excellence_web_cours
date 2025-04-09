<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdatePasswordsToPlainText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:plain-passwords {password=excellence123 : The plain text password to set for all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert all user passwords to plain text format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $password = $this->argument('password');
        
        $this->info('Updating all user passwords to plain text...');
        
        // Get all users
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->warn('No users found to update.');
            return 0;
        }
        
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();
        
        foreach ($users as $user) {
            // Update each user's password to plain text
            DB::table('users')
                ->where('id', $user->id)
                ->update(['password' => $password]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info('All user passwords have been updated to plain text successfully.');
        $this->line("New password for all users: {$password}");
        
        return Command::SUCCESS;
    }
}
