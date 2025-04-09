<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HighSchoolCommunicationCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $communicationCourses = [
            // Tronc Commun communication courses
            [
                'matiere' => 'Français Communication',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Anglais Communication',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Arabe Communication',
                'niveau_scolaire' => 'tronc_commun',
                'prix' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Deuxieme Annee communication courses
            [
                'matiere' => 'Français Communication',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Anglais Communication',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Arabe Communication',
                'niveau_scolaire' => 'deuxieme_annee',
                'prix' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Bac communication courses
            [
                'matiere' => 'Français Communication',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Anglais Communication',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matiere' => 'Arabe Communication',
                'niveau_scolaire' => 'bac',
                'prix' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Insert courses one by one to handle duplicates
        foreach ($communicationCourses as $course) {
            try {
                DB::table('communication_courses')->insert($course);
            } catch (\Exception $e) {
                // If course already exists, skip it
                continue;
            }
        }
    }
} 