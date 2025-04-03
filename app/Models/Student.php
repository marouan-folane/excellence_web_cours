<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'parent_name',
        'address',
        'course_id',
        'communication_course_id',
        'student_count',
        'niveau_scolaire',
        'status',
        'payment_expiry',
        'matiere',
        'paid_amount',
        'enrollment_date',
        'total_price',
        'months'
    ];

    protected $casts = [
        'student_count' => 'integer',
        'paid_amount' => 'float',
        'total_price' => 'float',
        'payment_expiry' => 'date',
        'enrollment_date' => 'date',
        'status' => 'string',
        'months' => 'integer'
    ];

    /**
     * Get the regular course that owns the student (using cours table)
     */
    public function course()
    {
        return $this->belongsTo(Cours::class, 'course_id');
    }
    
    /**
     * Get the regular course that owns the student
     */
    public function regularCourse()
    {
        return $this->belongsTo(Cours::class, 'course_id')->where('type', 'regular');
    }
    
    /**
     * Get the communication course that owns the student
     */
    public function communicationCourse()
    {
        return $this->belongsTo(CommunicationCourse::class, 'communication_course_id');
    }
    
    /**
     * Get the enrollments for the student
     */
    public function enrollments()
    {
        return $this->hasMany(StudentCourse::class);
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
     * Calculate total price based on courses and months
     */
    public function calculateTotalPrice()
    {
        $basePrice = 0;
        
        if ($this->course) {
            $basePrice += $this->course->prix;
        }
        
        if ($this->communicationCourse) {
            $basePrice += $this->communicationCourse->prix;
        }
        
        return $basePrice * ($this->months ?? 1);
    }

    /**
     * Scope a query to only include active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive students
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include students with expired payments
     */
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('payment_expiry')
              ->orWhere('payment_expiry', '<', Carbon::now()->startOfDay());
        });
    }

    /**
     * Scope a query to only include students with valid payments
     */
    public function scopeValid($query)
    {
        return $query->where('payment_expiry', '>=', Carbon::now()->startOfDay());
    }

    /**
     * Set the payment_expiry attribute
     */
    public function setPaymentExpiryAttribute($value)
    {
        $this->attributes['payment_expiry'] = Carbon::parse($value)->format('Y-m-d');
    }
    
    /**
     * Set the enrollment_date attribute
     */
    public function setEnrollmentDateAttribute($value)
    {
        $this->attributes['enrollment_date'] = Carbon::parse($value)->format('Y-m-d');
    }
}
