<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HighSchoolSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $highSchoolCourses = [
            // Tronc Commun courses
            [
                'matiere' => 'SVT',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Français',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Anglais',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Deuxieme Annee courses
            [
                'matiere' => 'SVT',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Français',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Anglais',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Bac courses
            [
                'matiere' => 'SVT',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Français',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Anglais',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'type' => 'regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Insert courses one by one to handle duplicates
        foreach ($highSchoolCourses as $course) {
            try {
                DB::table('cours')->insert($course);
            } catch (\Exception $e) {
                // If course already exists, skip it
                continue;
            }
        }
    }
} 