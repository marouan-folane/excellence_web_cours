<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Cours;
use App\Models\CommunicationCourse;
use App\Models\StudentCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $totalRevenue = Student::sum('paid_amount');
        
        // Group students by level
        $studentsByLevel = Student::select('niveau_scolaire', 
                                       DB::raw('SUM(student_count) as student_count'),
                                       DB::raw('SUM(total_price) as level_total_price'))
                              ->where('status', 'active')
                              ->groupBy('niveau_scolaire')
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
