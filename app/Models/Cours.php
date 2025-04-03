<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;

    protected $table = 'cours';
    
    protected $fillable = [
        'matiere',
        'niveau_scolaire',
        'prix',
        'type'
    ];

    protected $casts = [
        'prix' => 'float',
        'type' => 'string'
    ];

    /**
     * Get the students for the course
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'course_id');
    }

    /**
     * Scope a query to only include regular courses
     */
    public function scopeRegular($query)
    {
        return $query->where('type', 'regular');
    }

    /**
     * Scope a query to only include communication courses
     */
    public function scopeCommunication($query)
    {
        return $query->where('type', 'communication');
    }

    /**
     * Get the total revenue for this course
     */
    public function getTotalRevenue()
    {
        return $this->students()
                    ->active()
                    ->sum(\DB::raw('student_count * ' . $this->prix));
    }

    /**
     * Get the total number of students for this course
     */
    public function getTotalStudents()
    {
        return $this->students()
                    ->active()
                    ->sum('student_count');
    }

    /**
     * Get the enrollments for this course
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id');
    }
}
