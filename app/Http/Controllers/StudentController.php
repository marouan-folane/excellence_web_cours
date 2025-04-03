<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Cours;
use App\Models\CommunicationCourse;
use App\Models\StudentCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentController extends Controller
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
     * Display a listing of the students.
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

        if ($request->filled('course')) {
            $query->where(function($q) use ($request) {
                $q->where('course_id', $request->course)
                  ->orWhere('communication_course_id', $request->course);
            });
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

        $students = $query->latest()->paginate(25);
        
        // Get unique niveau_scolaire values
        $niveau_scolaires = [
            'premiere_school' => 'Première School',
            '2_first_middle_niveau' => '2nd First Middle Niveau',
            '3ac' => '3AC',
            'high_school' => 'High School'
        ];
        
        $courses = Cours::orderBy('matiere')->get();

        return view('students.index', compact('students', 'niveau_scolaires', 'courses'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $niveau_scolaires = [
            'premiere_school' => 'Première School (100 DH per subject)',
            '2_first_middle_niveau' => '2nd First Middle Niveau (100 DH/subject, SVT+PC: 150 DH)',
            '3ac' => '3AC (130 DH per subject)',
            'high_school' => 'High School (150 DH per subject)'
        ];

        // Get regular courses
        $courses = Cours::where('type', 'regular')
            ->orderBy('niveau_scolaire')
            ->orderBy('matiere')
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'matiere' => $course->matiere,
                    'niveau_scolaire' => $course->niveau_scolaire,
                    'prix' => $course->prix,
                    'type' => 'regular'
                ];
            });

        // Get communication courses from the correct model
        $communicationCourses = CommunicationCourse::orderBy('niveau_scolaire')
            ->orderBy('matiere')
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'matiere' => $course->matiere,
                    'niveau_scolaire' => $course->niveau_scolaire,
                    'prix' => $course->prix,
                    'type' => 'communication'
                ];
            });

        return view('students.create', compact(
            'niveau_scolaires',
            'courses',
            'communicationCourses'
        ));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'niveau_scolaire' => 'required|string',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:cours,id',
            'comm_course_ids' => 'nullable|array',
            'comm_course_ids.*' => 'exists:communication_courses,id',
            'payment_expiry' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'enrollment_date' => 'required|date',
            'months' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'student_count' => 'required|integer|min:1'
        ]);

        // Validate that at least one course is selected
        if (empty($validated['course_ids']) && empty($validated['comm_course_ids'])) {
            return redirect()->back()->withInput()->withErrors(['course_selection' => 'Please select at least one course']);
        }

        // Get selected regular courses
        $regularCourses = collect([]);
        if (!empty($validated['course_ids'])) {
            $regularCourses = Cours::whereIn('id', $validated['course_ids'])->get();
        }
        
        // Get selected communication courses
        $commCourses = collect([]);
        if (!empty($validated['comm_course_ids'])) {
            $commCourses = CommunicationCourse::whereIn('id', $validated['comm_course_ids'])->get();
        }
        
        // Calculate base price and get course names
        $basePrice = $regularCourses->sum('prix') + $commCourses->sum('prix');
        $totalPrice = $basePrice * $validated['months'];
        $courseNames = $regularCourses->pluck('matiere')->merge($commCourses->pluck('matiere'))->implode(', ');
        
        // Calculate payment expiry based on enrollment date and months
        $paymentExpiry = Carbon::parse($validated['enrollment_date'])->addMonths((int)$validated['months']);
        
        // Begin database transaction
        DB::beginTransaction();
        
        try {
            // Create student record
            $student = Student::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'parent_name' => $validated['parent_name'] ?? null,
                'address' => $validated['address'] ?? null,
                'niveau_scolaire' => $validated['niveau_scolaire'],
                'payment_expiry' => $paymentExpiry,
                'paid_amount' => $validated['paid_amount'],
                'status' => $validated['status'],
                'matiere' => $courseNames,
                'student_count' => $validated['student_count'],
                'enrollment_date' => $validated['enrollment_date'],
                'months' => $validated['months'],
                'total_price' => $totalPrice
            ]);

            // Create enrollment records for each regular course
            foreach ($regularCourses as $course) {
                $enrollment = new StudentCourse([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'enrollment_date' => $validated['enrollment_date'],
                    'payment_expiry' => $paymentExpiry,
                    'status' => 'active',
                    'months' => $validated['months'],
                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                ]);
                $enrollment->save();
            }
            
            // Create enrollment records for each communication course
            foreach ($commCourses as $course) {
                $enrollment = new StudentCourse([
                    'student_id' => $student->id,
                    'communication_course_id' => $course->id,
                    'enrollment_date' => $validated['enrollment_date'],
                    'payment_expiry' => $paymentExpiry,
                    'status' => 'active',
                    'months' => $validated['months'],
                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                ]);
                $enrollment->save();
            }
            
            DB::commit();
            
            return redirect()->route('students.index')
                ->with('success', 'Student enrolled successfully in ' . ($regularCourses->count() + $commCourses->count()) . ' course(s) for ' . $validated['months'] . ' month(s).');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Error creating student: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load(['course', 'communicationCourse', 'enrollments.course', 'enrollments.communicationCourse']);
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $student->load(['enrollments.course', 'enrollments.communicationCourse']);
        
        // Get all regular courses
        $courses = Cours::where('type', 'regular')
            ->orderBy('niveau_scolaire')
            ->orderBy('matiere')
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'matiere' => $course->matiere,
                    'niveau_scolaire' => $course->niveau_scolaire,
                    'prix' => $course->prix,
                    'type' => 'regular'
                ];
            });
            
        // Get all communication courses
        $communicationCourses = CommunicationCourse::orderBy('niveau_scolaire')
            ->orderBy('matiere')
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'matiere' => $course->matiere,
                    'niveau_scolaire' => $course->niveau_scolaire,
                    'prix' => $course->prix,
                    'type' => 'communication'
                ];
            });
            
        // Get student's current courses
        $selectedRegularCourseIds = $student->enrollments
            ->whereNotNull('course_id')
            ->pluck('course_id')
            ->toArray();
            
        $selectedCommCourseIds = $student->enrollments
            ->whereNotNull('communication_course_id')
            ->pluck('communication_course_id')
            ->toArray();

        return view('students.edit', compact('student', 'courses', 'communicationCourses', 'selectedRegularCourseIds', 'selectedCommCourseIds'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'niveau_scolaire' => 'required|string',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:cours,id',
            'comm_course_ids' => 'nullable|array',
            'comm_course_ids.*' => 'exists:communication_courses,id',
            'months' => 'required|integer|min:1',
            'student_count' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'payment_expiry' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'enrollment_date' => 'required|date'
        ]);
        
        // Validate that at least one course is selected
        if (empty($validated['course_ids']) && empty($validated['comm_course_ids'])) {
            return redirect()->back()->withInput()->withErrors(['course_selection' => 'Please select at least one course']);
        }

        // Get selected regular courses
        $regularCourses = collect([]);
        if (!empty($validated['course_ids'])) {
            $regularCourses = Cours::whereIn('id', $validated['course_ids'])->get();
        }
        
        // Get selected communication courses
        $commCourses = collect([]);
        if (!empty($validated['comm_course_ids'])) {
            $commCourses = CommunicationCourse::whereIn('id', $validated['comm_course_ids'])->get();
        }
        
        // Calculate base price and total price
        $basePrice = $regularCourses->sum('prix') + $commCourses->sum('prix');
        $totalPrice = $basePrice * $validated['months'];
        $courseNames = $regularCourses->pluck('matiere')->merge($commCourses->pluck('matiere'))->implode(', ');
        
        // Use payment_expiry directly instead of calculating based on enrollment_date + months
        // This ensures the payment expiry date from the form is used directly
        $paymentExpiry = Carbon::parse($validated['payment_expiry'])->format('Y-m-d');
        
        // Log for debugging
        \Log::info("Updating student {$student->id}", [
            'payment_expiry_form' => $validated['payment_expiry'],
            'payment_expiry_parsed' => $paymentExpiry
        ]);

        // Begin database transaction
        DB::beginTransaction();
        
        try {
            // Update student record with direct payment_expiry from the form
            $student->update([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'parent_name' => $validated['parent_name'] ?? null,
                'address' => $validated['address'] ?? null,
                'niveau_scolaire' => $validated['niveau_scolaire'],
                'payment_expiry' => $paymentExpiry,
                'paid_amount' => $validated['paid_amount'],
                'status' => $validated['status'],
                'matiere' => $courseNames,
                'student_count' => $validated['student_count'],
                'enrollment_date' => Carbon::parse($validated['enrollment_date'])->format('Y-m-d'),
                'months' => $validated['months'],
                'total_price' => $totalPrice
            ]);
            
            // Instead of deleting and recreating, we'll update existing enrollments and only create new ones
            
            // Get existing course enrollments
            $existingRegularEnrollments = $student->enrollments()
                ->whereNotNull('course_id')
                ->pluck('course_id')
                ->toArray();
                
            // Get existing communication course enrollments
            $existingCommEnrollments = $student->enrollments()
                ->whereNotNull('communication_course_id')
                ->pluck('communication_course_id')
                ->toArray();
            
            // Delete enrollments that are no longer needed
            $keepRegularCourseIds = $regularCourses->pluck('id')->toArray();
            $keepCommCourseIds = $commCourses->pluck('id')->toArray();
            
            // Delete regular course enrollments that aren't in the new selection
            $student->enrollments()
                ->whereNotNull('course_id')
                ->whereNotIn('course_id', $keepRegularCourseIds)
                ->delete();
                
            // Delete communication course enrollments that aren't in the new selection
            $student->enrollments()
                ->whereNotNull('communication_course_id')
                ->whereNotIn('communication_course_id', $keepCommCourseIds)
                ->delete();
            
            // Update existing enrollments and create new ones for regular courses
            foreach ($regularCourses as $course) {
                // Check if this enrollment already exists
                if (in_array($course->id, $existingRegularEnrollments)) {
                    // Update existing enrollment
                    $student->enrollments()
                        ->where('course_id', $course->id)
                        ->update([
                            'enrollment_date' => Carbon::parse($validated['enrollment_date'])->format('Y-m-d'),
                            'payment_expiry' => $paymentExpiry,
                            'status' => $validated['status'],
                            'months' => $validated['months'],
                            'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                        ]);
                } else {
                    try {
                        // First check if there's a soft-deleted record that might cause a duplicate key issue
                        $existingRecord = StudentCourse::withTrashed()
                            ->where('student_id', $student->id)
                            ->where('course_id', $course->id)
                            ->first();
                            
                        if ($existingRecord) {
                            // If it exists, restore and update it
                            $existingRecord->restore();
                            $existingRecord->update([
                                'enrollment_date' => Carbon::parse($validated['enrollment_date'])->format('Y-m-d'),
                                'payment_expiry' => $paymentExpiry,
                                'status' => $validated['status'],
                                'months' => $validated['months'],
                                'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                            ]);
                        } else {
                            // Create new enrollment if no existing record
                            $enrollment = new StudentCourse([
                                'student_id' => $student->id,
                                'course_id' => $course->id,
                                'enrollment_date' => Carbon::parse($validated['enrollment_date'])->format('Y-m-d'),
                                'payment_expiry' => $paymentExpiry,
                                'status' => $validated['status'],
                                'months' => $validated['months'],
                                'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                            ]);
                            $enrollment->save();
                        }
                    } catch (\Exception $e) {
                        // If there's a duplicate key error, find the record and update it
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            StudentCourse::where('student_id', $student->id)
                                ->where('course_id', $course->id)
                                ->update([
                                    'enrollment_date' => Carbon::parse($validated['enrollment_date'])->format('Y-m-d'),
                                    'payment_expiry' => $paymentExpiry,
                                    'status' => $validated['status'],
                                    'months' => $validated['months'],
                                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                                ]);
                        } else {
                            // If it's a different error, rethrow it
                            throw $e;
                        }
                    }
                }
            }
            
            // Update existing enrollments and create new ones for communication courses
            foreach ($commCourses as $course) {
                // Check if this enrollment already exists
                if (in_array($course->id, $existingCommEnrollments)) {
                    // Update existing enrollment
                    $student->enrollments()
                        ->where('communication_course_id', $course->id)
                        ->update([
                            'enrollment_date' => Carbon::parse($validated['enrollment_date'])->format('Y-m-d'),
                            'payment_expiry' => $paymentExpiry,
                            'status' => $validated['status'],
                            'months' => $validated['months'],
                            'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                        ]);
                } else {
                    try {
                        // First check if there's a soft-deleted record that might cause a duplicate key issue
                        $existingRecord = StudentCourse::withTrashed()
                            ->where('student_id', $student->id)
                            ->where('communication_course_id', $course->id)
                            ->first();
                            
                        if ($existingRecord) {
                            // If it exists, restore and update it
                            $existingRecord->restore();
                            $existingRecord->update([
                                'enrollment_date' => Carbon::parse($validated['enrollment_date'])->format('Y-m-d'),
                                'payment_expiry' => $paymentExpiry,
                                'status' => $validated['status'],
                                'months' => $validated['months'],
                                'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                            ]);
                        } else {
                            // Create new enrollment if no existing record
                            $enrollment = new StudentCourse([
                                'student_id' => $student->id,
                                'communication_course_id' => $course->id,
                                'enrollment_date' => Carbon::parse($validated['enrollment_date'])->format('Y-m-d'),
                                'payment_expiry' => $paymentExpiry,
                                'status' => $validated['status'],
                                'months' => $validated['months'],
                                'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                            ]);
                            $enrollment->save();
                        }
                    } catch (\Exception $e) {
                        // If there's a duplicate key error, find the record and update it
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            StudentCourse::where('student_id', $student->id)
                                ->where('communication_course_id', $course->id)
                                ->update([
                                    'enrollment_date' => Carbon::parse($validated['enrollment_date'])->format('Y-m-d'),
                                    'payment_expiry' => $paymentExpiry,
                                    'status' => $validated['status'],
                                    'months' => $validated['months'],
                                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months']
                                ]);
                        } else {
                            // If it's a different error, rethrow it
                            throw $e;
                        }
                    }
                }
            }
            
            DB::commit();
            
            // Refresh student to ensure all changes are loaded
            $student->refresh();
            
            return redirect()->route('students.index')
                ->with('success', 'Student enrollment updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Error updating student: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')
            ->with('success', 'Student enrollment deleted successfully.');
    }
    
    /**
     * Display monthly price summary by level.
     */
    public function monthlyPriceSummary()
    {
        $summaryByLevel = DB::table('students')
            ->join('cours', 'students.course_id', '=', 'cours.id')
            ->select(
                'students.niveau_scolaire',
                DB::raw('SUM(students.student_count * cours.prix) as total_price'),
                DB::raw('COUNT(DISTINCT students.id) as enrollment_count')
            )
            ->where('students.status', 'active')
            ->groupBy('students.niveau_scolaire')
            ->get();
            
        $totalMonthlyRevenue = $summaryByLevel->sum('total_price');
            
        return view('students.monthly-summary', compact('summaryByLevel', 'totalMonthlyRevenue'));
    }
    
    /**
     * Display students with payments expiring within 5 days.
     */
    public function nearExpiry(Request $request)
    {
        $fiveDaysLater = Carbon::now()->addDays(5);
        $today = Carbon::now();
        
        $query = Student::with(['course', 'communicationCourse'])
            ->where('status', 'active')
            ->where('payment_expiry', '>', $today)
            ->where('payment_expiry', '<=', $fiveDaysLater);
            
        // Apply level filter if provided
        if ($request->filled('level')) {
            $query->where('niveau_scolaire', $request->level);
        }
        
        $students = $query->orderBy('payment_expiry')
            ->paginate(25)
            ->withQueryString(); // Maintain query parameters in pagination links
            
        return view('students.near-expiry', compact('students'));
    }
}
