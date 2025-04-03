<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cours;
use App\Models\CommunicationCourse;
use App\Models\Student;
use App\Models\StudentCourse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    /**
     * Display enrollment form
     */
    public function showEnrollmentForm(Request $request)
    {
        $schoolLevels = [
            'premiere_school' => 'Première École',
            '2_first_middle_niveau' => '2ème Niveau Collège',
            '3ac' => '3éme Année Collège',
            'high_school' => 'Lycée'
        ];
        
        // Get regular courses grouped by level
        $regularCourses = Cours::where('type', 'regular')
            ->orderBy('niveau_scolaire')
            ->orderBy('matiere')
            ->get()
            ->groupBy('niveau_scolaire');
            
        // Get ALL communication courses (not filtered by level)
        $allCommunicationCourses = CommunicationCourse::orderBy('matiere')
            ->get();
            
        // Group communication courses by level for organization but will show all of them
        $communicationCourses = CommunicationCourse::orderBy('niveau_scolaire')
            ->orderBy('matiere')
            ->get()
            ->groupBy('niveau_scolaire');
            
        // Handle pre-selected level from URL
        $selectedLevel = $request->query('level');
        
        return view('enrollments.create', compact(
            'schoolLevels', 
            'regularCourses', 
            'communicationCourses',
            'allCommunicationCourses',
            'selectedLevel'
        ));
    }
    
    /**
     * Store a new enrollment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'niveau_scolaire' => 'required|string',
            'student_count' => 'required|integer|min:1',
            'months' => 'required|integer|min:1',
            'course_selections' => 'required|array|min:1',
            'course_selections.*' => 'string',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'parent_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);
        
        // Create the student record
        $student = new Student();
        $student->name = $validated['name'];
        $student->email = $validated['email'] ?? null;
        $student->phone = $validated['phone'];
        $student->parent_name = $validated['parent_name'] ?? null;
        $student->address = $validated['address'] ?? null;
        $student->niveau_scolaire = $validated['niveau_scolaire'];
        $student->student_count = $validated['student_count'];
        $student->status = 'active';
        $student->enrollment_date = Carbon::now();
        $student->payment_expiry = Carbon::now()->addMonths($validated['months']);
        $student->save();
        
        $totalPrice = 0;
        
        // Process course selections and create enrollments
        foreach ($validated['course_selections'] as $courseSelection) {
            list($type, $courseId) = explode(':', $courseSelection);
            
            $enrollment = new StudentCourse();
            $enrollment->student_id = $student->id;
            $enrollment->enrollment_date = Carbon::now();
            $enrollment->payment_expiry = Carbon::now()->addMonths($validated['months']);
            $enrollment->status = 'active';
            $enrollment->months = $validated['months'];
            
            if ($type === 'regular') {
                $course = Cours::findOrFail($courseId);
                $enrollment->course_id = $courseId;
                $enrollment->paid_amount = $course->prix * $student->student_count * $validated['months'];
            } else {
                $course = CommunicationCourse::findOrFail($courseId);
                $enrollment->communication_course_id = $courseId;
                $enrollment->paid_amount = $course->prix * $student->student_count * $validated['months'];
            }
            
            $enrollment->save();
            $totalPrice += $enrollment->paid_amount;
        }
        
        // Update student total price
        $student->total_price = $totalPrice;
        $student->paid_amount = $totalPrice; // Assuming full payment at enrollment
        $student->save();
        
        return redirect()->route('enrollments.index')
            ->with('success', 'Enrollment created successfully!');
    }
    
    /**
     * Show revenue by subject and level
     */
    public function revenueBySubject(Request $request)
    {
        // Get school levels
        $schoolLevels = [
            'premiere_school' => 'Première École',
            '2_first_middle_niveau' => '2ème Niveau Collège',
            '3ac' => '3éme Année Collège',
            'high_school' => 'Lycée'
        ];

        // Get revenue by regular course and level with filters
        $regularRevenueQuery = StudentCourse::join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('cours', 'student_courses.course_id', '=', 'cours.id')
            ->whereNotNull('student_courses.course_id')
            ->where('students.status', 'active');
            
        // Apply level filter if set
        if ($request->filled('level')) {
            $regularRevenueQuery->where('students.niveau_scolaire', $request->level);
        }
        
        // Apply subject search if set
        if ($request->filled('search')) {
            $regularRevenueQuery->where('cours.matiere', 'LIKE', '%' . $request->search . '%');
        }
        
        // Select and group query - make sure we count actual students, not just enrollments
        $regularRevenue = $regularRevenueQuery->select(
                'cours.matiere as subject',
                'students.niveau_scolaire as level',
                DB::raw('SUM(student_courses.paid_amount) as total_revenue'),
                DB::raw('SUM(students.student_count) as student_count'),
                DB::raw("'regular' as course_type")
            )
            ->groupBy('cours.matiere', 'students.niveau_scolaire')
            ->get();

        // Get revenue by communication course and level with filters
        $commRevenueQuery = StudentCourse::join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('communication_courses', 'student_courses.communication_course_id', '=', 'communication_courses.id')
            ->whereNotNull('student_courses.communication_course_id')
            ->where('students.status', 'active');
            
        // Apply level filter if set
        if ($request->filled('level')) {
            $commRevenueQuery->where('students.niveau_scolaire', $request->level);
        }
        
        // Apply subject search if set
        if ($request->filled('search')) {
            $commRevenueQuery->where('communication_courses.matiere', 'LIKE', '%' . $request->search . '%');
        }
        
        // Select and group query - ensure correct student count
        $commRevenue = $commRevenueQuery->select(
                'communication_courses.matiere as subject',
                'students.niveau_scolaire as level',
                DB::raw('SUM(student_courses.paid_amount) as total_revenue'),
                DB::raw('SUM(students.student_count) as student_count'),
                DB::raw("'communication' as course_type")
            )
            ->groupBy('communication_courses.matiere', 'students.niveau_scolaire')
            ->get();

        // Combine the results
        $revenueBySubject = collect();
        
        // Apply course type filter if set
        if ($request->filled('course_type')) {
            if ($request->course_type === 'regular') {
                $revenueBySubject = $regularRevenue;
            } elseif ($request->course_type === 'communication') {
                $revenueBySubject = $commRevenue;
            }
        } else {
            $revenueBySubject = $regularRevenue->concat($commRevenue);
        }
        
        // Track filtered count for display
        $filteredCount = $revenueBySubject->count();

        // Calculate totals by level - ensure we're counting actual student count
        $totalsByLevel = [];
        foreach ($schoolLevels as $levelKey => $levelName) {
            $levelItems = $revenueBySubject->where('level', $levelKey);
            $totalsByLevel[$levelKey] = [
                'revenue' => $levelItems->sum('total_revenue'),
                'students' => $levelItems->sum('student_count')
            ];
        }

        // Calculate grand total
        $grandTotal = $revenueBySubject->sum('total_revenue');
        $totalStudents = Student::sum('paid_amount');
  
        // Get actual students list for counting
        $studentsQuery = Student::where('status', 'active');
        if ($request->filled('level')) {
            $studentsQuery->where('niveau_scolaire', $request->level);
        }
        $actualStudents = $studentsQuery->get();
        $actualStudentCount = $actualStudents->sum('student_count');

        return view('enrollments.revenue', compact(
            'revenueBySubject', 
            'schoolLevels', 
            'totalsByLevel', 
            'grandTotal', 
            'totalStudents',
            'filteredCount',
            'actualStudentCount'
        ));
    }
    
    /**
     * Show enrollment summary for a specific level
     */
    public function summary($niveau_scolaire)
    {
        // Regular courses enrollments
        $regularEnrollments = DB::table('student_courses')
            ->join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('cours', 'student_courses.course_id', '=', 'cours.id')
            ->select(
                'cours.matiere',
                'cours.niveau_scolaire',
                'cours.prix',
                DB::raw('SUM(students.student_count) as total_students'),
                DB::raw('SUM(student_courses.paid_amount) as revenue'),
                DB::raw("'regular' as course_type")
            )
            ->where('students.niveau_scolaire', $niveau_scolaire)
            ->whereNotNull('student_courses.course_id')
            ->where('students.status', 'active')
            ->groupBy('cours.id', 'cours.matiere', 'cours.niveau_scolaire', 'cours.prix')
            ->get();
            
        // Communication courses enrollments
        $commEnrollments = DB::table('student_courses')
            ->join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('communication_courses', 'student_courses.communication_course_id', '=', 'communication_courses.id')
            ->select(
                'communication_courses.matiere',
                'communication_courses.niveau_scolaire',
                'communication_courses.prix',
                DB::raw('SUM(students.student_count) as total_students'),
                DB::raw('SUM(student_courses.paid_amount) as revenue'),
                DB::raw("'communication' as course_type")
            )
            ->where('students.niveau_scolaire', $niveau_scolaire)
            ->whereNotNull('student_courses.communication_course_id')
            ->where('students.status', 'active')
            ->groupBy('communication_courses.id', 'communication_courses.matiere', 'communication_courses.niveau_scolaire', 'communication_courses.prix')
            ->get();
            
        // Merge both collections
        $enrollments = $regularEnrollments->merge($commEnrollments);
        
        // Calculate total for this level
        $totalPrice = $enrollments->sum('revenue');
        
        // Format the level name for display
        $levelNames = [
            'premiere_school' => 'Première École',
            '2_first_middle_niveau' => '2ème Niveau Collège',
            '3ac' => '3éme Année Collège',
            'high_school' => 'Lycée'
        ];
        
        $levelName = $levelNames[$niveau_scolaire] ?? $niveau_scolaire;
        
        return view('enrollments.summary', compact('enrollments', 'totalPrice', 'niveau_scolaire', 'levelName'));
    }
    
    /**
     * Display summary by school level
     */
    public function summaryByLevel($level)
    {
        $schoolLevels = [
            'premiere_school' => 'Première École',
            '2_first_middle_niveau' => '2ème Niveau Collège',
            '3ac' => '3éme Année Collège',
            'high_school' => 'Lycée'
        ];
        
        if (!array_key_exists($level, $schoolLevels)) {
            abort(404, 'School level not found');
        }
        
        $levelName = $schoolLevels[$level];
        
        // Get regular enrollments for this level
        $regularEnrollments = StudentCourse::join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('cours', 'student_courses.course_id', '=', 'cours.id')
            ->where('students.niveau_scolaire', $level)
            ->where('student_courses.course_id', '!=', null)
            ->where('students.status', 'active')
            ->select(
                'cours.matiere as course_name',
                'cours.prix as price',
                DB::raw('SUM(students.student_count) as total_students'),
                DB::raw('SUM(student_courses.paid_amount) as total_revenue'),
                DB::raw("'regular' as course_type")
            )
            ->groupBy('cours.id', 'cours.matiere', 'cours.prix')
            ->get();
            
        // Get communication enrollments for this level
        $commEnrollments = StudentCourse::join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('communication_courses', 'student_courses.communication_course_id', '=', 'communication_courses.id')
            ->where('students.niveau_scolaire', $level)
            ->where('student_courses.communication_course_id', '!=', null)
            ->where('students.status', 'active')
            ->select(
                'communication_courses.matiere as course_name',
                'communication_courses.prix as price',
                DB::raw('SUM(students.student_count) as total_students'),
                DB::raw('SUM(student_courses.paid_amount) as total_revenue'),
                DB::raw("'communication' as course_type")
            )
            ->groupBy('communication_courses.id', 'communication_courses.matiere', 'communication_courses.prix')
            ->get();
            
        $enrollments = $regularEnrollments->concat($commEnrollments);
        
        $totalRevenue = $enrollments->sum('total_revenue');
        
        return view('enrollments.summary', compact('level', 'levelName', 'enrollments', 'totalRevenue'));
    }
    
    /**
     * Display index with summaries by level
     */
    public function index()
    {
        $enrollments = Student::with(['course', 'communicationCourse'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('enrollments.index', compact('enrollments'));
    }
} 