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
        // Create cours table
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->string('matiere', 100);
            $table->string('niveau_scolaire', 100);
            $table->decimal('prix', 10, 2);
            $table->string('type')->default('regular'); // 'regular' or 'communication'
            $table->timestamps();

            // Add composite unique constraint
            $table->unique(['matiere', 'niveau_scolaire', 'type']);
        });

        // Create communication_courses table
        Schema::create('communication_courses', function (Blueprint $table) {
            $table->id();
            $table->string('matiere', 100);
            $table->string('niveau_scolaire', 100);
            $table->decimal('prix', 10, 2);
            $table->timestamps();

            // Add composite unique constraint
            $table->unique(['matiere', 'niveau_scolaire']);
        });

        // Create students table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('parent_name')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('course_id')->nullable()->constrained('cours');
            $table->foreignId('communication_course_id')->nullable()->constrained('communication_courses');
            $table->integer('student_count');
            $table->string('niveau_scolaire', 100);
            $table->string('status')->default('active');
            $table->date('payment_expiry')->nullable();
            $table->string('matiere')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('enrollment_date')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });

        // Create enrollments table
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('course_id')->nullable();
            $table->integer('communication_course_id')->nullable();
            $table->timestamp('enrollment_date')->nullable();
            $table->timestamps();
        });

        // Create the function to calculate total price
        DB::unprepared("
            DROP FUNCTION IF EXISTS calculate_total_price;
            CREATE FUNCTION calculate_total_price(niveau VARCHAR(100)) 
            RETURNS DECIMAL(10,2)
            DETERMINISTIC
            BEGIN
                DECLARE regular_total DECIMAL(10,2);
                DECLARE comm_total DECIMAL(10,2);
                
                -- Calculate regular courses total
                SELECT COALESCE(SUM(c.prix * s.student_count), 0) INTO regular_total
                FROM cours c
                INNER JOIN students s ON c.id = s.course_id
                WHERE c.niveau_scolaire = niveau
                AND s.status = 'active';
                
                -- Calculate communication courses total
                SELECT COALESCE(SUM(cc.prix * s.student_count), 0) INTO comm_total
                FROM communication_courses cc
                INNER JOIN students s ON cc.id = s.communication_course_id
                WHERE cc.niveau_scolaire = niveau
                AND s.status = 'active';
                
                RETURN regular_total + comm_total;
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
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('communication_courses');
        Schema::dropIfExists('cours');
    }
};
