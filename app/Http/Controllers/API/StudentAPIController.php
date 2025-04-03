<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StudentAPIController extends Controller
{
    /**
     * Display a listing of the students.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Student::with(['course', 'communicationCourse']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('payment')) {
            if ($request->payment === 'valid') {
                $query->valid();
            } elseif ($request->payment === 'expired') {
                $query->expired();
            } elseif ($request->payment === 'near-expiry') {
                $fiveDaysLater = Carbon::now()->addDays(5);
                $today = Carbon::now();
                $query->where('payment_expiry', '>', $today)
                     ->where('payment_expiry', '<=', $fiveDaysLater);
            }
        }

        if ($request->filled('niveau_scolaire')) {
            $query->where('niveau_scolaire', $request->niveau_scolaire);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 25);
        $students = $query->latest()->paginate($perPage);
        
        return response()->json([
            'status' => 'success',
            'data' => $students
        ]);
    }

    /**
     * Store a newly created student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'niveau_scolaire' => 'required|string',
            'course_id' => 'nullable|exists:cours,id',
            'communication_course_id' => 'nullable|exists:communication_courses,id',
            'payment_expiry' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'enrollment_date' => 'required|date',
            'months' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'student_count' => 'required|integer|min:1',
            'matiere' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate that either course_id or communication_course_id is provided
        if (empty($request->course_id) && empty($request->communication_course_id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'At least one course must be selected'
            ], 422);
        }

        try {
            $student = Student::create($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Student created successfully',
                'data' => $student
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified student.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $student = Student::with(['course', 'communicationCourse', 'enrollments'])
                ->findOrFail($id);
                
            return response()->json([
                'status' => 'success',
                'data' => $student
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'niveau_scolaire' => 'sometimes|required|string',
            'course_id' => 'nullable|exists:cours,id',
            'communication_course_id' => 'nullable|exists:communication_courses,id',
            'payment_expiry' => 'sometimes|required|date',
            'paid_amount' => 'sometimes|required|numeric|min:0',
            'enrollment_date' => 'sometimes|required|date',
            'months' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|required|in:active,inactive',
            'student_count' => 'sometimes|required|integer|min:1',
            'matiere' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $student = Student::findOrFail($id);
            $student->update($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Student updated successfully',
                'data' => $student
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified student from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Student deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students with expiring payments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function nearExpiry()
    {
        $fiveDaysLater = Carbon::now()->addDays(5);
        $today = Carbon::now();
        
        $students = Student::where('payment_expiry', '>', $today)
            ->where('payment_expiry', '<=', $fiveDaysLater)
            ->where('status', 'active')
            ->with(['course', 'communicationCourse'])
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $students
        ]);
    }

    /**
     * Get monthly price summary.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function monthlyPriceSummary()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        
        $totalPayments = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->sum('paid_amount');
            
        $studentCount = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->count();
            
        return response()->json([
            'status' => 'success',
            'data' => [
                'month' => $currentMonth,
                'total_payments' => $totalPayments,
                'student_count' => $studentCount
            ]
        ]);
    }
} 