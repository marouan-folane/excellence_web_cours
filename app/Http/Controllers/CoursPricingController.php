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
        // Debug information
        \Log::info('Course creation request received', [
            'all_data' => $request->all(),
            'course_type' => $request->input('course_type'),
            'matiere' => $request->input('matiere'),
            'niveau_scolaire' => $request->input('niveau_scolaire'),
            'prix' => $request->input('prix'),
        ]);

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
            
            \Log::info('Regular course created', ['course_id' => $course->id]);
        } else {
            $validated = $request->validate([
                'matiere' => 'required|string|max:255',
                'niveau_scolaire' => 'required|string|max:255',
            ]);
            
            $course = new CommunicationCourse();
            $course->matiere = $validated['matiere'];
            $course->niveau_scolaire = $validated['niveau_scolaire'];
            $course->prix = $request->input('prix', 150); // Default to 150 if not set
            $course->save();
            
            \Log::info('Communication course created', ['course_id' => $course->id]);
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

    /**
     * Find a course by ID for detailed view
     */
    public function findCourse($id)
    {
        $course = Cours::findOrFail($id);
        
        // Get enrolled students for this course
        $enrolledStudents = DB::table('student_courses')
            ->select('students.*', 'student_courses.enrollment_date', 'student_courses.payment_expiry', 'student_courses.status')
            ->join('students', 'student_courses.student_id', '=', 'students.id')
            ->where('student_courses.course_id', $id)
            ->whereNull('student_courses.deleted_at')
            ->get();
        
        return view('courses.details', [
            'course' => $course,
            'enrolledStudents' => $enrolledStudents
        ]);
    }

    /**
     * Delete the specified course from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the course
            $course = Cours::findOrFail($id);
            
            // Check if course has any active students enrolled
            $hasActiveEnrollments = DB::table('student_courses')
                ->where('course_id', $id)
                ->whereNull('deleted_at')
                ->exists();
            
            if ($hasActiveEnrollments) {
                return redirect()->route('courses.manage')
                    ->with('error', 'Cannot delete course because it has active enrollments.');
            }
            
            // Delete the course
            $course->delete();
            
            return redirect()->route('courses.manage')
                ->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('courses.manage')
                ->with('error', 'Error deleting course: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit($id)
    {
        $course = Cours::findOrFail($id);
        return view('courses.edit', compact('course'));
    }

    /**
     * Show the form for confirming course deletion.
     */
    public function delete($id)
    {
        $course = Cours::findOrFail($id);
        return view('courses.delete', compact('course'));
    }

    /**
     * Display enrollments for a specific course.
     */
    public function courseEnrollments($id)
    {
        $course = Cours::findOrFail($id);
        $enrollments = $course->enrollments()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('courses.enrollments', [
            'course' => $course,
            'enrollments' => $enrollments
        ]);
    }

    protected $levelNames = [
        'premiere_school' => 'Première School',
        '1ac' => '1st Middle School',
        '2ac' => '2nd Middle School',
        '3ac' => '3AC',
        'tronc_commun' => 'Tronc Commun',
        'deuxieme_annee' => '2ème Année Lycée',
        'bac' => 'Baccalauréat'
    ];
}
