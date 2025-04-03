<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\CommunicationCourse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Enrollment;
use Illuminate\Validation\Rule;

class CoursPricingController extends Controller
{
    /**
     * Display a listing of all courses with pricing.
     */
    public function index()
    {
        // Get all courses grouped by school level
        $regularCourses = Cours::where('type', 'regular')->get()->groupBy('niveau_scolaire');
        $communicationCourses = CommunicationCourse::all()->groupBy('niveau_scolaire');
        
        return view('courses.index', [
            'regularCourses' => $regularCourses,
            'communicationCourses' => $communicationCourses
        ]);
    }
    
    /**
     * Display the course pricing by school level.
     */
    public function showByLevel($level)
    {
        // Get courses for a specific school level
        $regularCourses = Cours::where('type', 'regular')->where('niveau_scolaire', $level)->get();
        $communicationCourses = CommunicationCourse::where('niveau_scolaire', $level)->get();
        
        return view('courses.by_level', [
            'level' => $level,
            'regularCourses' => $regularCourses,
            'communicationCourses' => $communicationCourses
        ]);
    }
    
    /**
     * Store a new student enrollment.
     */
    public function enroll(Request $request)
    {
        // Validate the request
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required_without:communication_course_id|exists:cours,id',
            'communication_course_id' => 'required_without:course_id|exists:communication_courses,id',
        ]);

        // Check if the student is already enrolled in this course
        $existingEnrollment = Enrollment::where('student_id', $request->student_id)
            ->where(function($query) use ($request) {
                if ($request->has('course_id')) {
                    $query->where('course_id', $request->course_id);
                } else {
                    $query->where('communication_course_id', $request->communication_course_id);
                }
            })
            ->exists();

        if ($existingEnrollment) {
            return redirect()->back()->with('error', 'Student is already enrolled in this course.');
        }

        // Create a new enrollment
        $enrollment = new Enrollment();
        $enrollment->student_id = $request->student_id;
        
        if ($request->has('course_id')) {
            $enrollment->course_id = $request->course_id;
            $course = Cours::find($request->course_id);
            $enrollment->is_combined = false; // Simplified - adjust as needed
            $enrollment->is_communication = false;
        } else {
            $enrollment->communication_course_id = $request->communication_course_id;
            $enrollment->is_combined = false;
            $enrollment->is_communication = true;
        }
        
        $enrollment->status = 'active';
        $enrollment->save();

        return redirect()->back()->with('success', 'Student enrolled successfully.');
    }
    
    /**
     * Show enrollment summary.
     */
    public function enrollmentSummary()
    {
        // Get summary of enrollments
        $summary = DB::table('enrollments')
            ->leftJoin('cours as rc', 'enrollments.course_id', '=', 'rc.id')
            ->leftJoin('communication_courses as cc', 'enrollments.communication_course_id', '=', 'cc.id')
            ->leftJoin('students', 'enrollments.student_id', '=', 'students.id')
            ->select(
                DB::raw('COALESCE(rc.niveau_scolaire, cc.niveau_scolaire) as niveau_scolaire'),
                DB::raw('COUNT(DISTINCT students.id) as student_count'),
                DB::raw('SUM(CASE WHEN enrollments.is_combined = 1 THEN rc.prix * 0.8 ELSE COALESCE(rc.prix, 150) END) as total_revenue')
            )
            ->groupBy('niveau_scolaire')
            ->get();

        // Calculate total revenue
        $totalRevenue = $this->calculate_total_price();

        return view('courses.summary', [
            'summary' => $summary,
            'totalRevenue' => $totalRevenue
        ]);
    }
    
    /**
     * Calculate the monthly total price for a level.
     */
    private function calculate_total_price()
    {
        // Get active enrollments and calculate price
        $regularCourseRevenue = DB::table('enrollments')
            ->join('cours', 'enrollments.course_id', '=', 'cours.id')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->where('students.status', 'active')
            ->select(
                DB::raw('SUM(CASE WHEN enrollments.is_combined = 1 THEN cours.prix * 0.8 ELSE cours.prix END) as total')
            )
            ->value('total') ?? 0;
            
        $communicationCourseRevenue = DB::table('enrollments')
            ->join('communication_courses', 'enrollments.communication_course_id', '=', 'communication_courses.id')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->where('students.status', 'active')
            ->count() * 150; // Communication courses are fixed at 150 DH
            
        return $regularCourseRevenue + $communicationCourseRevenue;
    }
    
    /**
     * Display the course management interface.
     */
    public function manage()
    {
        // Get courses for management
        $regularCourses = Cours::where('type', 'regular')->withCount([
            'enrollments as active_enrollments' => function ($query) {
                $query->whereHas('student', function($q) {
                    $q->where('status', 'active');
                });
            }
        ])->get();
        
        $communicationCourses = CommunicationCourse::withCount([
            'enrollments as active_enrollments' => function ($query) {
                $query->whereHas('student', function($q) {
                    $q->where('status', 'active');
                });
            }
        ])->get();
        
        $students = Student::all();
        
        // Get course enrollment statistics
        $regularCourseEnrollments = DB::table('cours')
            ->select(
                'cours.id',
                'cours.matiere as name',
                'cours.niveau_scolaire',
                DB::raw("'regular' as type"),
                DB::raw('COUNT(DISTINCT e.student_id) as enrolled_count'),
                DB::raw('SUM(cours.prix) as revenue')
            )
            ->leftJoin('enrollments as e', 'cours.id', '=', 'e.course_id')
            ->leftJoin('students as s', 'e.student_id', '=', 's.id')
            ->where('cours.type', 'regular')
            ->where('s.status', 'active')
            ->groupBy('cours.id', 'cours.matiere', 'cours.niveau_scolaire')
            ->get();
            
        $commCourseEnrollments = DB::table('communication_courses')
            ->select(
                'communication_courses.id',
                'communication_courses.matiere as name',
                'communication_courses.niveau_scolaire',
                DB::raw("'communication' as type"),
                DB::raw('COUNT(DISTINCT e.student_id) as enrolled_count'),
                DB::raw('SUM(communication_courses.prix) as revenue')
            )
            ->leftJoin('enrollments as e', 'communication_courses.id', '=', 'e.communication_course_id')
            ->leftJoin('students as s', 'e.student_id', '=', 's.id')
            ->where('s.status', 'active')
            ->groupBy('communication_courses.id', 'communication_courses.matiere', 'communication_courses.niveau_scolaire')
            ->get();
            
        // Combine both collections
        $courseEnrollments = $regularCourseEnrollments->concat($commCourseEnrollments);
        
        return view('courses.manage', [
            'regularCourses' => $regularCourses,
            'communicationCourses' => $communicationCourses,
            'students' => $students,
            'courseEnrollments' => $courseEnrollments
        ]);
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        // Validate and store a new course
        if ($request->input('course_type') === 'regular') {
            $validated = $request->validate([
                'matiere' => 'required|string|max:255',
                'niveau_scolaire' => 'required|string|max:255',
                'prix' => 'required|numeric|min:0',
            ]);
            
            $course = new Cours();
            $course->matiere = $validated['matiere'];
            $course->niveau_scolaire = $validated['niveau_scolaire'];
            $course->prix = $validated['prix'];
            $course->type = 'regular';
            // No longer using is_combined and combined_with fields
            $course->save();
        } else {
            $validated = $request->validate([
                'matiere' => 'required|string|max:255',
                'niveau_scolaire' => 'required|string|max:255',
            ]);
            
            $course = new CommunicationCourse();
            $course->matiere = $validated['matiere'];
            $course->niveau_scolaire = $validated['niveau_scolaire'];
            $course->save();
        }

        return redirect()->route('courses.manage')->with('success', 'Course created successfully.');
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate and update an existing course
        if ($request->input('course_type') === 'regular') {
            $course = Cours::findOrFail($id);
            
            $validated = $request->validate([
                'matiere' => 'required|string|max:255',
                'niveau_scolaire' => 'required|string|max:255',
                'prix' => 'required|numeric|min:0',
            ]);
            
            $course->matiere = $validated['matiere'];
            $course->niveau_scolaire = $validated['niveau_scolaire'];
            $course->prix = $validated['prix'];
            // Maintain the type as 'regular'
            $course->type = 'regular';
        } else {
            $course = CommunicationCourse::findOrFail($id);
            
            $validated = $request->validate([
                'matiere' => 'required|string|max:255',
                'niveau_scolaire' => 'required|string|max:255',
            ]);
            
            $course->matiere = $validated['matiere'];
            $course->niveau_scolaire = $validated['niveau_scolaire'];
        }
        
        $course->save();
        
        return redirect()->route('courses.manage')->with('success', 'Course updated successfully.');
    }
}
