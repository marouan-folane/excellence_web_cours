<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check database connection and list tables';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking database connection...');
        
        try {
            $connection = DB::connection()->getPdo();
            $this->info("Connected successfully to database: " . env('DB_DATABASE'));
            
            $this->info("\nDatabase tables:");
            $tables = DB::select('SHOW TABLES');
            
            $headers = ['Table Name'];
            $tableData = [];
            
            foreach ($tables as $table) {
                $tableName = reset($table);
                $tableData[] = [$tableName];
                
                // Get row count for each table
                $count = DB::table($tableName)->count();
                $this->line("- $tableName ($count records)");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Connection failed: " . $e->getMessage());
            return 1;
        }
    }
} 