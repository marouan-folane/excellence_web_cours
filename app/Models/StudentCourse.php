<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StudentCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student_courses';

    protected $fillable = [
        'student_id',
        'course_id',
        'communication_course_id',
        'enrollment_date',
        'payment_expiry',
        'paid_amount',
        'months',
        'status'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'payment_expiry' => 'date',
        'paid_amount' => 'float',
        'status' => 'string'
    ];

    /**
     * Get the student that owns the enrollment
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course for this enrollment
     */
    public function course()
    {
        return $this->belongsTo(Cours::class);
    }

    /**
     * Get the communication course for this enrollment
     */
    public function communicationCourse()
    {
        return $this->belongsTo(CommunicationCourse::class);
    }

    /**
     * Check if the payment is expired
     */
    public function isPaymentExpired()
    {
        if (!$this->payment_expiry) {
            return true;
        }
        return Carbon::now()->startOfDay()->gt($this->payment_expiry);
    }

    /**
     * Get the remaining days until payment expiry
     */
    public function getRemainingDays()
    {
        if (!$this->payment_expiry) {
            return 0;
        }
        return max(0, Carbon::now()->startOfDay()->diffInDays($this->payment_expiry, false));
    }

    /**
     * Scope a query to only include active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive enrollments
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
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
} 