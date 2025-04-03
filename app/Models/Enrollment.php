<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'communication_course_id',
        'enrollment_date'
    ];

    protected $casts = [
        'enrollment_date' => 'datetime',
    ];

    /**
     * Get the student that owns the enrollment
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the course for this enrollment
     */
    public function course()
    {
        return $this->belongsTo(Cours::class, 'course_id');
    }

    /**
     * Get the communication course for this enrollment
     */
    public function communicationCourse()
    {
        return $this->belongsTo(CommunicationCourse::class, 'communication_course_id');
    }

    /**
     * Get the course price
     */
    public function getPrice()
    {
        if ($this->course_id) {
            return $this->course->prix;
        } elseif ($this->communication_course_id) {
            return $this->communicationCourse->prix;
        }
        return 0;
    }

    /**
     * Get the course type
     */
    public function getCourseType()
    {
        if ($this->course_id) {
            return 'regular';
        } elseif ($this->communication_course_id) {
            return 'communication';
        }
        return null;
    }

    /**
     * Get the course name
     */
    public function getCourseName()
    {
        if ($this->course_id) {
            return $this->course->matiere;
        } elseif ($this->communication_course_id) {
            return $this->communicationCourse->matiere;
        }
        return null;
    }

    /**
     * Get the school level
     */
    public function getSchoolLevel()
    {
        if ($this->course_id) {
            return $this->course->niveau_scolaire;
        } elseif ($this->communication_course_id) {
            return $this->communicationCourse->niveau_scolaire;
        }
        return null;
    }
} 