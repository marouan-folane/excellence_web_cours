<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HighSchoolCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the high school courses
        $highSchoolCourses = [
            // Tronc Commun courses
            [
                'matiere' => 'Mathematics',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Physics',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Chemistry',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Biology',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Deuxieme Annee Lycee courses
            [
                'matiere' => 'Mathematics',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Physics',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Chemistry',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Biology',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Baccalaureat courses
            [
                'matiere' => 'Mathematics',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Physics',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Chemistry',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Biology',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert the courses
        DB::table('cours')->insert($highSchoolCourses);
    }
} 