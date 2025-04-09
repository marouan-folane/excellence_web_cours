<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Cours;
use App\Models\CommunicationCourse;
use App\Models\StudentCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Count total students (sum of student_count field, not just count of records)
        $studentsCount = Student::where('status', 'active')->sum('student_count');
        
        // Count total courses (both regular and communication)
        $coursesCount = Cours::count() + CommunicationCourse::count();
        
        // Count total enrollments
        $enrollmentsCount = StudentCourse::count();
        
        // Calculate total revenue (considering months paid)
        // Using the same calculation method as in ReportController
        $totalRevenue = 0;
        $activeEnrollments = StudentCourse::where('status', 'active')
            ->where(function($query) {
                $query->where('enrollment_date', '<=', Carbon::now())
                      ->orWhereNull('enrollment_date');
            })
            ->get();
            
        foreach ($activeEnrollments as $enrollment) {
            // Get the remaining months of enrollment from now
            $now = Carbon::now()->startOfMonth();
            $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
            
            if ($endDate && $now->lte($endDate)) {
                // Get monthly revenue for all remaining months
                $currentMonth = $now->copy();
                while ($currentMonth->lte($endDate)) {
                    $totalRevenue += $enrollment->getRevenueForMonth($currentMonth);
                    $currentMonth->addMonth();
                }
            } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                // If payment is still valid but within the current month
                $totalRevenue += $enrollment->getRevenueForMonth(Carbon::now());
            }
        }
        
        // Group students by level
        $studentsByLevel = Student::select('niveau_scolaire', 
                               DB::raw('SUM(student_count) as student_count'),
                               DB::raw('SUM(CASE WHEN enrollment_date <= CURDATE() OR enrollment_date IS NULL THEN total_price ELSE 0 END) as level_total_price'))
                      ->where('status', 'active')
                      ->groupBy('niveau_scolaire')
                      ->orderByRaw("FIELD(niveau_scolaire, 'premiere_school', '1ac', '2ac', '3ac', 'high_school')")
                      ->get();
        
        // Get recent enrollments
        $recentEnrollments = StudentCourse::with(['student', 'course', 'communicationCourse'])
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();
        
        return view('dashboard', compact(
            'studentsCount', 
            'coursesCount', 
            'enrollmentsCount', 
            'totalRevenue',
            'studentsByLevel',
            'recentEnrollments'
        ));
    }
}
