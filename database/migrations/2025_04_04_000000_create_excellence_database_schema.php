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
        // Create cache table
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        // Create cache_locks table
        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        // Create communication_courses table
        if (!Schema::hasTable('communication_courses')) {
            Schema::create('communication_courses', function (Blueprint $table) {
                $table->id();
                $table->string('matiere', 100);
                $table->string('niveau_scolaire', 100);
                $table->decimal('prix', 10, 2)->default(150.00);
                $table->timestamps();
                
                $table->unique(['matiere', 'niveau_scolaire']);
            });
        }

        // Create cours table
        if (!Schema::hasTable('cours')) {
            Schema::create('cours', function (Blueprint $table) {
                $table->id();
                $table->string('matiere', 100);
                $table->string('niveau_scolaire', 100);
                $table->decimal('prix', 10, 2);
                $table->string('type')->default('regular');
                $table->timestamps();
                
                $table->unique(['matiere', 'niveau_scolaire', 'type']);
            });
        }

        // Create failed_jobs table
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // Create jobs table
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        // Create job_batches table
        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        // Create password_reset_tokens table
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Create sessions table
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // Create users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('username', 50)->unique();
                $table->string('email', 100)->unique();
                $table->string('password', 255);
                $table->timestamp('created_at')->useCurrent();
                $table->rememberToken();
            });
        }

        // Create students table
        if (!Schema::hasTable('students')) {
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
                $table->decimal('paid_amount', 10, 2)->default(0.00);
                $table->date('enrollment_date')->nullable();
                $table->integer('months')->default(1);
                $table->decimal('total_price', 10, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // Create student_courses table
        if (!Schema::hasTable('student_courses')) {
            Schema::create('student_courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->nullable()->constrained('cours')->onDelete('set null');
                $table->foreignId('communication_course_id')->nullable()->constrained('communication_courses')->onDelete('set null');
                $table->date('enrollment_date');
                $table->date('payment_expiry')->nullable();
                $table->decimal('paid_amount', 10, 2)->default(0.00);
                $table->decimal('monthly_revenue_amount', 10, 2)->default(0.00)->comment('Monthly revenue amount');
                $table->string('status')->default('active');
                $table->timestamps();
                $table->string('months')->nullable();
                $table->softDeletes();
                
                $table->unique(['student_id', 'course_id']);
                $table->unique(['student_id', 'communication_course_id']);
            });
        }

        // Create enrollments table 
        if (!Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table) {
                $table->id();
                $table->integer('student_id');
                $table->integer('course_id')->nullable();
                $table->integer('communication_course_id')->nullable();
                $table->timestamp('enrollment_date')->nullable();
                $table->timestamps();
            });
        }

        // Create calculate_total_price function
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
        // Drop function
        DB::unprepared('DROP FUNCTION IF EXISTS `calculate_total_price`');

        // Drop tables in reverse order to avoid foreign key constraints
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('student_courses');
        Schema::dropIfExists('students');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('cours');
        Schema::dropIfExists('communication_courses');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('users');
    }
}; 