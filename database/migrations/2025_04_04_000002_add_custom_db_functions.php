<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create calculate_total_price function if it doesn't exist
        DB::unprepared('
            DROP FUNCTION IF EXISTS `calculate_total_price`;
            CREATE FUNCTION `calculate_total_price`(`niveau` VARCHAR(100)) RETURNS DECIMAL(10,2) DETERMINISTIC 
            BEGIN
                DECLARE regular_total DECIMAL(10,2);
                DECLARE comm_total DECIMAL(10,2);
                
                -- Calculate regular courses total
                SELECT COALESCE(SUM(c.prix * s.student_count), 0) INTO regular_total
                FROM cours c
                INNER JOIN students s ON c.id = s.course_id
                WHERE c.niveau_scolaire = niveau
                AND s.status = "active";
                
                -- Calculate communication courses total
                SELECT COALESCE(SUM(cc.prix * s.student_count), 0) INTO comm_total
                FROM communication_courses cc
                INNER JOIN students s ON cc.id = s.communication_course_id
                WHERE cc.niveau_scolaire = niveau
                AND s.status = "active";
                
                RETURN regular_total + comm_total;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the function if it exists
        DB::unprepared('DROP FUNCTION IF EXISTS `calculate_total_price`');
    }
}; 