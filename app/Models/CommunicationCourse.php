<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationCourse extends Model
{
    use HasFactory;

    protected $table = 'communication_courses';
    
    protected $fillable = [
        'matiere',
        'niveau_scolaire',
        'prix'
    ];

    /**
     * Get the students for the communication course
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'communication_course_id');
    }

    /**
     * Get the enrollments for this communication course
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'communication_course_id');
    }
}
