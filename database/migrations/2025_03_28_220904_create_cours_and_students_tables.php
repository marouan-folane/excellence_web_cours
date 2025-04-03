<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create regular courses table
        Schema::create('regular_courses', function (Blueprint $table) {
            $table->id();
            $table->string('matiere', 100);
            $table->string('niveau_scolaire', 100);
            $table->decimal('prix', 10, 2);
            $table->boolean('is_combined')->default(false);
            $table->string('combined_with')->nullable();
            $table->timestamps();
        });

        // Create communication courses table
        Schema::create('communication_courses', function (Blueprint $table) {
            $table->id();
            $table->string('matiere', 100);
            $table->string('niveau_scolaire', 100);
            $table->decimal('prix', 10, 2)->default(150.00);
            $table->timestamps();
        });

        // Create students table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained('regular_courses');
            $table->foreignId('communication_course_id')->nullable()->constrained('communication_courses');
            $table->integer('student_count');
            $table->string('niveau_scolaire', 100);
            $table->string('status')->default('active');
            $table->date('payment_expiry')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        // Create the function to calculate total price
        DB::unprepared("
            DROP FUNCTION IF EXISTS calculate_total_price;
            CREATE FUNCTION calculate_total_price(niveau VARCHAR(100)) 
            RETURNS DECIMAL(10,2)
            DETERMINISTIC
            BEGIN
                DECLARE total DECIMAL(10,2);
                DECLARE combined_total DECIMAL(10,2);
                
                -- Calculate regular courses total
                SET total = (
                    SELECT COALESCE(SUM(rc.prix * s.student_count), 0)
                    FROM regular_courses rc
                    INNER JOIN students s ON rc.id = s.course_id
                    WHERE rc.niveau_scolaire = niveau
                    AND s.status = 'active'
                    AND rc.is_combined = false
                );
                
                -- Add combined courses total (svt+pc)
                SET combined_total = (
                    SELECT COALESCE(SUM(rc.prix * s.student_count), 0)
                    FROM regular_courses rc
                    INNER JOIN students s ON rc.id = s.course_id
                    WHERE rc.niveau_scolaire = niveau
                    AND s.status = 'active'
                    AND rc.is_combined = true
                );
                
                -- Add communication courses total
                SET total = total + combined_total + (
                    SELECT COALESCE(SUM(cc.prix * s.student_count), 0)
                    FROM communication_courses cc
                    INNER JOIN students s ON cc.id = s.communication_course_id
                    WHERE cc.niveau_scolaire = niveau
                    AND s.status = 'active'
                );
                
                RETURN total;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the function first
        DB::unprepared('DROP FUNCTION IF EXISTS calculate_total_price');

        // Drop tables in reverse order of creation
        Schema::dropIfExists('students');
        Schema::dropIfExists('communication_courses');
        Schema::dropIfExists('regular_courses');
    }
};
