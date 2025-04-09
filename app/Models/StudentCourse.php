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
        'monthly_revenue_amount',
        'months',
        'status'
    ];

    protected $casts = [
        'enrollment_date' => 'datetime',
        'payment_expiry' => 'datetime',
        'paid_amount' => 'float',
        'monthly_revenue_amount' => 'float',
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

    /**
     * Get the name of the course (either regular or communication)
     */
    public function getCourseName()
    {
        if ($this->course_id) {
            return $this->course ? $this->course->matiere : 'N/A';
        } elseif ($this->communication_course_id) {
            return $this->communicationCourse ? $this->communicationCourse->matiere : 'N/A';
        }
        return 'N/A';
    }
    
    /**
     * Get the type of course (regular or communication)
     */
    public function getCourseType()
    {
        if ($this->course_id) {
            return 'regular';
        } elseif ($this->communication_course_id) {
            return 'communication';
        }
        return 'unknown';
    }
    
    /**
     * Calculate the monthly revenue for this enrollment
     * 
     * @return float
     */
    public function calculateMonthlyRevenue()
    {
        $totalPaid = $this->paid_amount;
        $monthsCount = intval($this->months);
        
        if ($monthsCount <= 0) {
            $monthsCount = 1;
        }
        
        return round($totalPaid / $monthsCount, 2);
    }
    
    /**
     * Get revenue for a specific month
     * 
     * @param string|Carbon $monthDate A date within the target month
     * @return float The revenue for that month or 0 if not applicable
     */
    public function getRevenueForMonth($monthDate)
    {
        if (!$this->enrollment_date || !$this->payment_expiry) {
            return 0;
        }
        
        $targetMonth = $monthDate instanceof Carbon 
            ? $monthDate->copy()->startOfMonth() 
            : Carbon::parse($monthDate)->startOfMonth();
        
        $enrollmentStart = $this->enrollment_date->copy()->startOfMonth();
        $paymentEnd = $this->payment_expiry->copy()->startOfMonth();
        
        // Check if the target month is within the enrollment period
        if ($targetMonth->lt($enrollmentStart) || $targetMonth->gt($paymentEnd)) {
            return 0;
        }
        
        // If we have a monthly_revenue_amount, use it
        if ($this->monthly_revenue_amount > 0) {
            return $this->monthly_revenue_amount;
        }
        
        // Otherwise calculate it from the total paid amount
        return $this->calculateMonthlyRevenue();
    }
    
    /**
     * Get all monthly revenues for this enrollment
     * 
     * @return array Array of monthly revenues with month as key
     */
    public function getAllMonthlyRevenues()
    {
        if (!$this->enrollment_date || !$this->payment_expiry) {
            return [];
        }
        
        $result = [];
        $monthlyAmount = $this->monthly_revenue_amount > 0 
            ? $this->monthly_revenue_amount 
            : $this->calculateMonthlyRevenue();
        
        $currentMonth = $this->enrollment_date->copy()->startOfMonth();
        $lastMonth = $this->payment_expiry->copy()->startOfMonth();
        
        while ($currentMonth->lte($lastMonth)) {
            $key = $currentMonth->format('Y-m');
            $result[$key] = $monthlyAmount;
            $currentMonth->addMonth();
        }
        
        return $result;
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