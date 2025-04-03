<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Seed users
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'email' => 'admin@excellence.com',
                'password_hash' => Hash::make('password'),
                'created_at' => '2023-03-30 08:00:00',
                'remember_token' => null
            ],
            [
                'username' => 'manager',
                'email' => 'manager@excellence.com',
                'password_hash' => Hash::make('password'),
                'created_at' => '2023-03-30 08:00:00',
                'remember_token' => null
            ],
            [
                'username' => 'staff',
                'email' => 'staff@excellence.com',
                'password_hash' => Hash::make('password'),
                'created_at' => '2023-03-30 08:00:00',
                'remember_token' => null
            ]
        ]);

        // Seed communication courses
        DB::table('communication_courses')->insert([
            [
                'matiere' => 'communication_anglais',
                'niveau_scolaire' => 'premiere_school',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'communication_francais',
                'niveau_scolaire' => 'premiere_school',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'communication_anglais',
                'niveau_scolaire' => '2_first_middle_niveau',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'communication_francais',
                'niveau_scolaire' => '2_first_middle_niveau',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'communication_anglais',
                'niveau_scolaire' => '3ac',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'communication_francais',
                'niveau_scolaire' => '3ac',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'communication_anglais',
                'niveau_scolaire' => 'high_school',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'communication_francais',
                'niveau_scolaire' => 'high_school',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ]
        ]);

        // Seed cours (regular courses)
        DB::table('cours')->insert([
            [
                'matiere' => 'global',
                'niveau_scolaire' => 'premiere_school',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'math',
                'niveau_scolaire' => '2_first_middle_niveau',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'francais',
                'niveau_scolaire' => '2_first_middle_niveau',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'anglais',
                'niveau_scolaire' => '2_first_middle_niveau',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'svt+pc',
                'niveau_scolaire' => '2_first_middle_niveau',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'math',
                'niveau_scolaire' => '3ac',
                'prix' => 130.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'francais',
                'niveau_scolaire' => '3ac',
                'prix' => 130.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'anglais',
                'niveau_scolaire' => '3ac',
                'prix' => 130.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'pc',
                'niveau_scolaire' => '3ac',
                'prix' => 130.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'svt',
                'niveau_scolaire' => '3ac',
                'prix' => 130.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'math',
                'niveau_scolaire' => 'high_school',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'francais',
                'niveau_scolaire' => 'high_school',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'anglais',
                'niveau_scolaire' => 'high_school',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'pc',
                'niveau_scolaire' => 'high_school',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'svt',
                'niveau_scolaire' => 'high_school',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear data from tables
        DB::table('cours')->truncate();
        DB::table('communication_courses')->truncate();
        DB::table('users')->truncate();
    }
}; 