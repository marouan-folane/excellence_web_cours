<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('cours')->onDelete('set null');
            $table->foreignId('communication_course_id')->nullable()->constrained('communication_courses')->onDelete('set null');
            $table->date('enrollment_date');
            $table->date('payment_expiry')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->integer('months')->default(1);
            $table->string('status')->default('active');
            $table->timestamps();
            
            // Make sure each student is enrolled in a course only once
            $table->unique(['student_id', 'course_id']);
            $table->unique(['student_id', 'communication_course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_courses');
    }
}; 