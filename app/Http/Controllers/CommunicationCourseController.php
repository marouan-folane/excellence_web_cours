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
} 