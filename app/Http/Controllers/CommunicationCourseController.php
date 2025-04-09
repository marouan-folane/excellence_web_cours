<?php

namespace App\Http\Controllers;

use App\Models\CommunicationCourse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunicationCourseController extends Controller
{
    /**
     * Display a listing of communication courses.
     */
    public function index()
    {
        $courses = CommunicationCourse::withCount(['students as total_students' => function($query) {
            $query->select(DB::raw('SUM(student_count)'));
        }])
        ->withSum(['students as total_revenue' => function($query) {
            $query->select(DB::raw('student_count * prix'));
        }], 'student_count')
        ->get()
        ->groupBy('niveau_scolaire');

        return view('communication-courses.index', compact('courses'));
    }

    /**
     * Show courses for a specific level.
     */
    public function showByLevel($niveau_scolaire)
    {
        $courses = CommunicationCourse::where('niveau_scolaire', $niveau_scolaire)
            ->withCount(['students as total_students' => function($query) {
                $query->select(DB::raw('SUM(student_count)'));
            }])
            ->get();

        return view('communication-courses.level', compact('courses', 'niveau_scolaire'));
    }

    /**
     * Show the course management page.
     */
    public function manage()
    {
        $courses = CommunicationCourse::withCount(['students as total_students' => function($query) {
            $query->select(DB::raw('SUM(student_count)'));
        }])->get();

        return view('communication-courses.manage', compact('courses'));
    }

    /**
     * Enroll students in a communication course.
     */
    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:communication_courses,id',
            'student_count' => 'required|integer|min:1',
            'niveau_scolaire' => 'required|string'
        ]);

        $student = Student::create([
            'communication_course_id' => $validated['course_id'],
            'student_count' => $validated['student_count'],
            'niveau_scolaire' => $validated['niveau_scolaire']
        ]);

        return redirect()->route('communication-courses.enrollment.summary')
            ->with('success', 'Students enrolled successfully!');
    }

    /**
     * Show enrollment summary.
     */
    public function enrollmentSummary()
    {
        $enrollments = Student::whereNotNull('communication_course_id')
            ->with('communicationCourse')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('communication-courses.enrollment-summary', compact('enrollments'));
    }

    /**
     * Find a communication course by ID for detailed view
     */
    public function findCourse($id)
    {
        $course = CommunicationCourse::findOrFail($id);
        
        // Get enrolled students for this course
        $enrolledStudents = DB::table('student_courses')
            ->select('students.*', 'student_courses.enrollment_date', 'student_courses.payment_expiry', 'student_courses.status')
            ->join('students', 'student_courses.student_id', '=', 'students.id')
            ->where('student_courses.communication_course_id', $id)
            ->whereNull('student_courses.deleted_at')
            ->get();
        
        return view('communication-courses.details', [
            'course' => $course,
            'enrolledStudents' => $enrolledStudents
        ]);
    }

    /**
     * Store a newly created communication course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'matiere' => 'required|string|max:255',
            'niveau_scolaire' => 'required|string|max:255',
        ]);
        
        // Create a new communication course
        $course = new CommunicationCourse();
        $course->matiere = $validated['matiere'];
        $course->niveau_scolaire = $validated['niveau_scolaire'];
        $course->prix = $request->input('prix', 150); // Default to 150 if not provided
        $course->save();
        
        return redirect()->route('communication-courses.index')->with('success', 'Communication course created successfully.');
    }

    /**
     * Update the specified communication course.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'matiere' => 'required|string|max:255',
            'niveau_scolaire' => 'required|string|max:255',
        ]);
        
        // Find and update the course
        $course = CommunicationCourse::findOrFail($id);
        $course->matiere = $validated['matiere'];
        $course->niveau_scolaire = $validated['niveau_scolaire'];
        $course->prix = $request->input('prix', 150); // Ensure price is updated properly
        $course->save();
        
        return redirect()->route('courses.manage')->with('success', 'Communication course updated successfully.');
    }

    /**
     * Delete the specified communication course from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the course
            $course = CommunicationCourse::findOrFail($id);
            
            // Check if course has any active students enrolled
            $hasActiveEnrollments = DB::table('student_courses')
                ->where('communication_course_id', $id)
                ->whereNull('deleted_at')
                ->exists();
            
            if ($hasActiveEnrollments) {
                return redirect()->route('communication-courses.manage')
                    ->with('error', 'Cannot delete course because it has active enrollments.');
            }
            
            // Delete the course
            $course->delete();
            
            return redirect()->route('communication-courses.manage')
                ->with('success', 'Communication course deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('communication-courses.manage')
                ->with('error', 'Error deleting course: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new communication course.
     */
    public function create()
    {
        return view('courses.communication.create');
    }

    /**
     * Show the form for editing the specified communication course.
     */
    public function edit($id)
    {
        $course = CommunicationCourse::findOrFail($id);
        return view('courses.communication.edit', compact('course'));
    }

    /**
     * Show the form for confirming communication course deletion.
     */
    public function delete($id)
    {
        $course = CommunicationCourse::findOrFail($id);
        return view('courses.communication.delete', compact('course'));
    }

    /**
     * Display enrollments for a specific communication course.
     */
    public function courseEnrollments($id)
    {
        $course = CommunicationCourse::findOrFail($id);
        $enrollments = $course->enrollments()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('courses.communication.enrollments', [
            'course' => $course,
            'enrollments' => $enrollments
        ]);
    }
} 