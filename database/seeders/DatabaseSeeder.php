<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'username' => 'admin',
            'email' => 'admin@excellence.com',
            'password' => Hash::make('admin123'),
        ]);

        // Create manager user
        User::create([
            'username' => 'manager',
            'email' => 'manager@excellence.com',
            'password' => Hash::make('manager123'),
        ]);

        // Create staff user
        User::create([
            'username' => 'staff',
            'email' => 'staff@excellence.com',
            'password' => Hash::make('staff123'),
        ]);

        // Create test user
        User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create some demo users using factory
        User::factory()->count(5)->create();

        // Call the course seeders
       
    }
}
