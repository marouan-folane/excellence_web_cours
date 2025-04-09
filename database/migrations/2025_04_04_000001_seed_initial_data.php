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
                'password' => Hash::make('password'),
                'created_at' => '2023-03-30 08:00:00',
                'remember_token' => null
            ],
            [
                'username' => 'manager',
                'email' => 'manager@excellence.com',
                'password' => Hash::make('password'),
                'created_at' => '2023-03-30 08:00:00',
                'remember_token' => null
            ],
            [
                'username' => 'staff',
                'email' => 'staff@excellence.com',
                'password' => Hash::make('password'),
                'created_at' => '2023-03-30 08:00:00',
                'remember_token' => null
            ]
        ]);

        // Seed communication courses
        DB::table('communication_courses')->insert([
            // Premiere school
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
            
            // 1ac (split from 2_first_middle_niveau)
            [
                'matiere' => 'communication_anglais',
                'niveau_scolaire' => '1ac',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'communication_francais',
                'niveau_scolaire' => '1ac',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            
            // 2ac (split from 2_first_middle_niveau)
            [
                'matiere' => 'communication_anglais',
                'niveau_scolaire' => '2ac',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'communication_francais',
                'niveau_scolaire' => '2ac',
                'prix' => 150.00,
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            
            // 3ac
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
            
            // High school
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
            // Premiere school
            [
                'matiere' => 'global',
                'niveau_scolaire' => 'premiere_school',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            
            // 1ac courses (split from 2_first_middle_niveau)
            [
                'matiere' => 'math',
                'niveau_scolaire' => '1ac',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'francais',
                'niveau_scolaire' => '1ac',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'anglais',
                'niveau_scolaire' => '1ac',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'svt+pc',
                'niveau_scolaire' => '1ac',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            
            // 2ac courses (split from 2_first_middle_niveau)
            [
                'matiere' => 'math',
                'niveau_scolaire' => '2ac',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'francais',
                'niveau_scolaire' => '2ac',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'anglais',
                'niveau_scolaire' => '2ac',
                'prix' => 100.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            [
                'matiere' => 'svt+pc',
                'niveau_scolaire' => '2ac',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => '2023-03-30 08:00:00',
                'updated_at' => '2023-03-30 08:00:00'
            ],
            
            // 3ac courses
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
            
            // High school courses
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