<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'excellenceuser',
            'email' => 'user@excellence.com',
            'password' => 'excellence123', // Plain text password, no longer hashed
            'created_at' => now(),
            'remember_token' => null
        ]);

        // Feedback to console
        $this->command->info('User created successfully: user@excellence.com (password: excellence123)');
    }
} 