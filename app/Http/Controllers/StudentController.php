<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Cours;
use App\Models\CommunicationCourse;
use App\Models\StudentCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;

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
            '1ac' => '1st Middle School',
            '2ac' => '2nd Middle School',
            '3ac' => '3AC',
            'tronc_commun' => 'Tronc Commun',
            'deuxieme_annee' => '2ème Année Lycée',
            'bac' => 'Baccalauréat'
        ];
        
        $courses = Cours::orderBy('matiere')->get();

        return view('students.index', compact('students', 'niveau_scolaires', 'courses'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        // Add pricing info
        $niveau_scolaires = [
            'premiere_school' => 'Première School (100 DH/subject)',
            '1ac' => '1st Middle School (100 DH/subject, SVT+PC: 150 DH)',
            '2ac' => '2nd Middle School (100 DH/subject, SVT+PC: 150 DH)',
            '3ac' => '3AC (130 DH/subject)',
            'tronc_commun' => 'Tronc Commun (150 DH/subject)',
            'deuxieme_annee' => 'Deuxième Année Lycée (150 DH/subject)',
            'bac' => 'Baccalauréat (150 DH/subject)'
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
        
        // Create Carbon instances for enrollment date and payment expiry
        $enrollmentDate = Carbon::parse($validated['enrollment_date']);
        
        // Calculate payment expiry based on enrollment date and months
        // For example, if enrollment_date is 2023-09-01 and months is 3, expiry should be 2023-12-01
        $paymentExpiry = $enrollmentDate->copy()->addMonths((int)$validated['months']);
        
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
                'enrollment_date' => $enrollmentDate,
                'months' => $validated['months'],
                'total_price' => $totalPrice
            ]);

            // Create enrollment records for each regular course
            foreach ($regularCourses as $course) {
                $monthlyRevenue = ($course->prix * $validated['student_count']) / 1; // Monthly amount
                
                $enrollment = new StudentCourse([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'enrollment_date' => $enrollmentDate,
                    'payment_expiry' => $paymentExpiry,
                    'status' => 'active',
                    'months' => $validated['months'],
                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                    'monthly_revenue_amount' => $monthlyRevenue
                ]);
                $enrollment->save();
            }
            
            // Create enrollment records for each communication course
            foreach ($commCourses as $course) {
                $monthlyRevenue = ($course->prix * $validated['student_count']) / 1; // Monthly amount
                
                $enrollment = new StudentCourse([
                    'student_id' => $student->id,
                    'communication_course_id' => $course->id,
                    'enrollment_date' => $enrollmentDate,
                    'payment_expiry' => $paymentExpiry,
                    'status' => 'active',
                    'months' => $validated['months'],
                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                    'monthly_revenue_amount' => $monthlyRevenue
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

        $niveau_scolaires = [
            'premiere_school' => 'Première School',
            '1ac' => '1st Middle School',
            '2ac' => '2nd Middle School',
            '3ac' => '3AC',
            'tronc_commun' => 'Tronc Commun',
            'deuxieme_annee' => '2ème Année Lycée',
            'bac' => 'Baccalauréat'
        ];

        return view('students.edit', compact('student', 'courses', 'communicationCourses', 'selectedRegularCourseIds', 'selectedCommCourseIds', 'niveau_scolaires'));
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
            'student_count' => 'nullable|integer|min:1', // Made optional as we'll default to 1
            'status' => 'required|in:active,inactive',
            'payment_expiry' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'enrollment_date' => 'required|date'
        ]);
        
        // Always use 1 for student_count
        $validated['student_count'] = 1;
        
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
        
        // Parse the dates properly
        $enrollmentDate = Carbon::parse($request->enrollment_date)->format('Y-m-d');
        $paymentExpiry = Carbon::parse($request->payment_expiry)->format('Y-m-d');
        
        // Log for debugging
        \Log::info("Updating student {$student->id} - Dates before SQL update", [
            'enrollment_date_input' => $request->enrollment_date,
            'payment_expiry_input' => $request->payment_expiry,
            'enrollment_date_parsed' => $enrollmentDate,
            'payment_expiry_parsed' => $paymentExpiry
        ]);

        // Begin database transaction
        DB::beginTransaction();
        
        try {
            // Update student record directly in the database to bypass model mutators
            DB::table('students')
                ->where('id', $student->id)
                ->update([
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
                    'enrollment_date' => $enrollmentDate,
                    'months' => $validated['months'],
                    'total_price' => $totalPrice,
                    'updated_at' => now()
                ]);
            
            // Reload the student to get fresh data
            $student = Student::find($student->id);
            
            // Log for debugging student update
            \Log::info("Student updated - ID: {$student->id} - Dates after update", [
                'enrollment_date' => $student->enrollment_date,
                'payment_expiry' => $student->payment_expiry
            ]);
            
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
            
            // Only delete if specific course IDs were provided
            if (isset($validated['course_ids'])) {
                // Delete regular course enrollments that aren't in the new selection
                $student->enrollments()
                    ->whereNotNull('course_id')
                    ->whereNotIn('course_id', $keepRegularCourseIds)
                    ->delete();
            }
                
            // Only delete if specific course IDs were provided
            if (isset($validated['comm_course_ids'])) {
                // Delete communication course enrollments that aren't in the new selection
                $student->enrollments()
                    ->whereNotNull('communication_course_id')
                    ->whereNotIn('communication_course_id', $keepCommCourseIds)
                    ->delete();
            }
            
            // Update existing enrollments and create new ones for regular courses
            foreach ($regularCourses as $course) {
                // Check if this enrollment already exists
                if (in_array($course->id, $existingRegularEnrollments)) {
                    // Update using direct SQL to avoid model mutators
                    DB::table('student_courses')
                        ->where('student_id', $student->id)
                        ->where('course_id', $course->id)
                        ->whereNull('deleted_at')
                        ->update([
                            'enrollment_date' => $enrollmentDate,
                            'payment_expiry' => $paymentExpiry,
                            'status' => $validated['status'],
                            'months' => $validated['months'],
                            'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                            'monthly_revenue_amount' => $course->prix * $validated['student_count'],
                            'updated_at' => now()
                        ]);
                } else {
                    try {
                        // First check if there's a soft-deleted record that might cause a duplicate key issue
                        $existingRecord = StudentCourse::withTrashed()
                            ->where('student_id', $student->id)
                            ->where('course_id', $course->id)
                            ->first();
                            
                        if ($existingRecord) {
                            // If it exists, restore and update it using direct SQL
                            $existingRecord->restore();
                            DB::table('student_courses')
                                ->where('id', $existingRecord->id)
                                ->update([
                                    'enrollment_date' => $enrollmentDate,
                                    'payment_expiry' => $paymentExpiry,
                                    'status' => $validated['status'],
                                    'months' => $validated['months'],
                                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                                    'monthly_revenue_amount' => $course->prix * $validated['student_count'],
                                    'updated_at' => now(),
                                    'deleted_at' => null
                                ]);
                        } else {
                            // Create new enrollment using direct SQL
                            DB::table('student_courses')->insert([
                                'student_id' => $student->id,
                                'course_id' => $course->id,
                                'enrollment_date' => $enrollmentDate,
                                'payment_expiry' => $paymentExpiry,
                                'status' => $validated['status'],
                                'months' => $validated['months'],
                                'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                                'monthly_revenue_amount' => $course->prix * $validated['student_count'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    } catch (\Exception $e) {
                        // If there's a duplicate key error, find the record and update it
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            DB::table('student_courses')
                                ->where('student_id', $student->id)
                                ->where('course_id', $course->id)
                                ->update([
                                    'enrollment_date' => $enrollmentDate,
                                    'payment_expiry' => $paymentExpiry,
                                    'status' => $validated['status'],
                                    'months' => $validated['months'],
                                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                                    'monthly_revenue_amount' => $course->prix * $validated['student_count'],
                                    'updated_at' => now()
                                ]);
                        } else {
                            throw $e;
                        }
                    }
                }
            }
            
            // Update existing enrollments and create new ones for communication courses
            foreach ($commCourses as $course) {
                if (in_array($course->id, $existingCommEnrollments)) {
                    // Update using direct SQL to avoid model mutators
                    DB::table('student_courses')
                        ->where('student_id', $student->id)
                        ->where('communication_course_id', $course->id)
                        ->whereNull('deleted_at')
                        ->update([
                            'enrollment_date' => $enrollmentDate,
                            'payment_expiry' => $paymentExpiry,
                            'status' => $validated['status'],
                            'months' => $validated['months'],
                            'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                            'monthly_revenue_amount' => $course->prix * $validated['student_count'],
                            'updated_at' => now()
                        ]);
                } else {
                    try {
                        // Check for soft-deleted records
                        $existingRecord = StudentCourse::withTrashed()
                            ->where('student_id', $student->id)
                            ->where('communication_course_id', $course->id)
                            ->first();
                            
                        if ($existingRecord) {
                            // Restore and update using direct SQL
                            $existingRecord->restore();
                            DB::table('student_courses')
                                ->where('id', $existingRecord->id)
                                ->update([
                                    'enrollment_date' => $enrollmentDate,
                                    'payment_expiry' => $paymentExpiry,
                                    'status' => $validated['status'],
                                    'months' => $validated['months'],
                                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                                    'monthly_revenue_amount' => $course->prix * $validated['student_count'],
                                    'updated_at' => now(),
                                    'deleted_at' => null
                                ]);
                        } else {
                            // Create new record using direct SQL
                            DB::table('student_courses')->insert([
                                'student_id' => $student->id,
                                'communication_course_id' => $course->id,
                                'enrollment_date' => $enrollmentDate,
                                'payment_expiry' => $paymentExpiry,
                                'status' => $validated['status'],
                                'months' => $validated['months'],
                                'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                                'monthly_revenue_amount' => $course->prix * $validated['student_count'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Handle duplicate key errors
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            DB::table('student_courses')
                                ->where('student_id', $student->id)
                                ->where('communication_course_id', $course->id)
                                ->update([
                                    'enrollment_date' => $enrollmentDate,
                                    'payment_expiry' => $paymentExpiry,
                                    'status' => $validated['status'],
                                    'months' => $validated['months'],
                                    'paid_amount' => $course->prix * $validated['student_count'] * $validated['months'],
                                    'monthly_revenue_amount' => $course->prix * $validated['student_count'],
                                    'updated_at' => now()
                                ]);
                        } else {
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
            ->where('payment_expiry', '<=', $fiveDaysLater)
            // Only include students whose enrollment has already started
            ->where(function($q) use ($today) {
                $q->where('enrollment_date', '<=', $today)
                  ->orWhereNull('enrollment_date');
            });
            
        // Apply level filter if provided
        if ($request->filled('level')) {
            $query->where('niveau_scolaire', $request->level);
        }
        
        $students = $query->orderBy('payment_expiry')
            ->paginate(25)
            ->withQueryString(); // Maintain query parameters in pagination links
            
        return view('students.near-expiry', compact('students'));
    }

    /**
     * Generate a receipt for a student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateReceipt(Request $request, $id)
    {
        // Get selected language from request
        $pdfLanguage = $request->input('pdf_language', app()->getLocale());
        
        // Temporarily switch to selected language for PDF generation
        $originalLocale = app()->getLocale();
        app()->setLocale($pdfLanguage);
        
        $student = Student::with(['enrollments.course', 'enrollments.communicationCourse'])->findOrFail($id);
        
        // Prepare course data
        $courses = [];
        foreach ($student->enrollments as $enrollment) {
            $course = $enrollment->course ?? $enrollment->communicationCourse ?? null;
            if (!$course) continue;
            
            $courses[] = (object)[
                'course' => (object)[
                    'name' => $course->matiere,
                    'type' => $enrollment->course ? 'regular' : 'communication',
                    'prix' => $course->prix
                ]
            ];
        }
        
        // Calculate current monthly revenue
        $student->current_monthly_revenue = 0;
        $currentMonth = Carbon::now()->startOfMonth();
        
        foreach ($student->enrollments as $enrollment) {
            $student->current_monthly_revenue += $enrollment->monthly_revenue_amount ?? 0;
        }
        
        // Generate receipt number
        $receipt_number = 'RCPT-' . str_pad($student->id, 5, '0', STR_PAD_LEFT) . '-' . date('Ymd');
        
        // Configure PDF options
        $options = [
            'default_font' => 'DejaVu Sans'
        ];
        
        // Set RTL mode if Arabic is the selected language
        if ($pdfLanguage === 'ar') {
            $options['isRtl'] = true;
        }
        
        // Configure PDF
        $pdf = PDF::loadView('students.receipt', [
            'student' => $student,
            'courses' => $courses,
            'receipt_number' => $receipt_number
        ]);
        
        // Apply RTL and other configurations for Arabic
        if ($pdfLanguage === 'ar') {
            $pdf->setOption('isRtl', true);
            $pdf->setOption('defaultFont', 'DejaVu Sans');
            $pdf->setPaper('a4');
        }
        
        // Restore original locale
        app()->setLocale($originalLocale);
        
        return $pdf->stream("receipt_{$student->id}.pdf");
    }
}
