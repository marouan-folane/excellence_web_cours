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
            '1ac' => '1ère Année Collège',
            '2ac' => '2ème Année Collège',
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
        
        // Get redirect parameter if it exists
        $redirect = $request->query('redirect');
        
        return view('enrollments.create', compact(
            'schoolLevels', 
            'regularCourses', 
            'communicationCourses',
            'allCommunicationCourses',
            'selectedLevel',
            'redirect'
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
            'enrollment_date' => 'nullable|date',
            'redirect' => 'nullable|string', // Add redirect parameter
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
        $student->enrollment_date = Carbon::parse($validated['enrollment_date'] ?? now());
        $student->payment_expiry = Carbon::parse($validated['enrollment_date'] ?? now())->addMonths((int)$validated['months']);
        $student->save();
        
        $totalPrice = 0;
        
        // Process course selections and create enrollments
        foreach ($validated['course_selections'] as $courseSelection) {
            list($type, $courseId) = explode(':', $courseSelection);
            
            $enrollment = new StudentCourse();
            $enrollment->student_id = $student->id;
            $enrollment->enrollment_date = Carbon::parse($validated['enrollment_date'] ?? now());
            $enrollment->payment_expiry = Carbon::parse($validated['enrollment_date'] ?? now())->addMonths((int)$validated['months']);
            $enrollment->status = 'active';
            $enrollment->months = $validated['months'];
            
            if ($type === 'regular') {
                $course = Cours::findOrFail($courseId);
                $enrollment->course_id = $courseId;
                $enrollment->paid_amount = $course->prix * $student->student_count * $validated['months'];
                // Calculate monthly revenue
                $enrollment->monthly_revenue_amount = $course->prix * $student->student_count;
            } else {
                $course = CommunicationCourse::findOrFail($courseId);
                $enrollment->communication_course_id = $courseId;
                $enrollment->paid_amount = $course->prix * $student->student_count * $validated['months'];
                // Calculate monthly revenue
                $enrollment->monthly_revenue_amount = $course->prix * $student->student_count;
            }
            
            $enrollment->save();
            $totalPrice += $enrollment->paid_amount;
        }
        
        // Update student total price
        $student->total_price = $totalPrice;
        $student->paid_amount = $totalPrice; // Assuming full payment at enrollment
        $student->save();
        
        // Check if we need to redirect to a specific page
        if (isset($validated['redirect']) && $validated['redirect'] === 'dashboard') {
            return redirect()->route('dashboard')
                ->with('success', 'Enrollment created successfully!');
        }
        
        // Default redirect to enrollments index
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
            '1ac' => '1ère Année Collège',
            '2ac' => '2ème Année Collège',
            '3ac' => '3éme Année Collège',
            'high_school' => 'Lycée'
        ];

        // Get active enrollments
        $activeEnrollments = StudentCourse::with(['student', 'course', 'communicationCourse'])
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('enrollment_date', '<=', Carbon::now())
                      ->orWhereNull('enrollment_date');
            });
            
        // Apply level filter if set
        if ($request->filled('level')) {
            $activeEnrollments->whereHas('student', function($query) use ($request) {
                $query->where('niveau_scolaire', $request->level);
            });
        }
        
        // Apply subject search if set
        if ($request->filled('search')) {
            $activeEnrollments->where(function($query) use ($request) {
                $query->whereHas('course', function($q) use ($request) {
                    $q->where('matiere', 'LIKE', '%' . $request->search . '%');
                })->orWhereHas('communicationCourse', function($q) use ($request) {
                    $q->where('matiere', 'LIKE', '%' . $request->search . '%');
                });
            });
        }
        
        // Get enrollments
        $enrollments = $activeEnrollments->get();
        
        // Process regular courses
        $regularRevenueData = [];
        $regularEnrollments = $enrollments->filter(function($enrollment) {
            return $enrollment->course_id !== null;
        });
        
        foreach ($regularEnrollments as $enrollment) {
            $subject = $enrollment->course->matiere;
            $level = $enrollment->student->niveau_scolaire;
            
            // Calculate remaining months revenue
            $startDate = Carbon::now()->startOfMonth();
            $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
            $monthlyRevenue = 0;
            
            if ($endDate && $startDate->lte($endDate)) {
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $monthlyRevenue += $enrollment->getRevenueForMonth($currentDate);
                    $currentDate->addMonth();
                }
            } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                $monthlyRevenue += $enrollment->getRevenueForMonth(Carbon::now());
            }
            
            $key = $subject . '-' . $level;
            
            if (!isset($regularRevenueData[$key])) {
                $regularRevenueData[$key] = [
                    'subject' => $subject,
                    'level' => $level,
                    'total_revenue' => 0,
                    'student_count' => 0,
                    'course_type' => 'regular'
                ];
            }
            
            $regularRevenueData[$key]['total_revenue'] += $monthlyRevenue;
            $regularRevenueData[$key]['student_count'] += $enrollment->student->student_count;
        }
        
        // Process communication courses
        $commRevenueData = [];
        $commEnrollments = $enrollments->filter(function($enrollment) {
            return $enrollment->communication_course_id !== null;
        });
        
        foreach ($commEnrollments as $enrollment) {
            $subject = $enrollment->communicationCourse->matiere;
            $level = $enrollment->student->niveau_scolaire;
            
            // Calculate remaining months revenue
            $startDate = Carbon::now()->startOfMonth();
            $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
            $monthlyRevenue = 0;
            
            if ($endDate && $startDate->lte($endDate)) {
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $monthlyRevenue += $enrollment->getRevenueForMonth($currentDate);
                    $currentDate->addMonth();
                }
            } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                $monthlyRevenue += $enrollment->getRevenueForMonth(Carbon::now());
            }
            
            $key = $subject . '-' . $level;
            
            if (!isset($commRevenueData[$key])) {
                $commRevenueData[$key] = [
                    'subject' => $subject,
                    'level' => $level,
                    'total_revenue' => 0,
                    'student_count' => 0,
                    'course_type' => 'communication'
                ];
            }
            
            $commRevenueData[$key]['total_revenue'] += $monthlyRevenue;
            $commRevenueData[$key]['student_count'] += $enrollment->student->student_count;
        }
        
        // Convert to collections of objects (not arrays)
        $regularItems = collect();
        foreach (array_values($regularRevenueData) as $item) {
            $regularItems->push((object)$item);
        }
        
        $commItems = collect();
        foreach (array_values($commRevenueData) as $item) {
            $commItems->push((object)$item);
        }

        // Combine the results
        $revenueBySubject = collect();
        
        // Apply course type filter if set
        if ($request->filled('course_type')) {
            if ($request->course_type === 'regular') {
                $revenueBySubject = $regularItems;
            } elseif ($request->course_type === 'communication') {
                $revenueBySubject = $commItems;
            }
        } else {
            $revenueBySubject = $regularItems->concat($commItems);
        }
        
        // Track filtered count for display
        $filteredCount = $revenueBySubject->count();

        // Calculate totals
        $totalRevenue = $revenueBySubject->sum('total_revenue');
        $totalStudents = $revenueBySubject->sum('student_count');
        $grandTotal = $totalRevenue;
        
        // Calculate totals by level
        $totalsByLevel = [];
        foreach ($schoolLevels as $levelKey => $levelName) {
            $levelItems = $revenueBySubject->where('level', $levelKey);
            $totalsByLevel[$levelKey] = [
                'revenue' => $levelItems->sum('total_revenue'),
                'students' => $levelItems->sum('student_count')
            ];
        }
  
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
            'totalRevenue', 
            'totalStudents',
            'totalsByLevel',
            'grandTotal',
            'filteredCount',
            'actualStudentCount'
        ));
    }
    
    /**
     * Show enrollment summary for a specific level
     */
    public function summary($niveau_scolaire)
    {
        // Get active enrollments for this level
        $activeEnrollments = StudentCourse::with(['student', 'course', 'communicationCourse'])
            ->whereHas('student', function($query) use ($niveau_scolaire) {
                $query->where('niveau_scolaire', $niveau_scolaire)
                      ->where('status', 'active');
            })
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('enrollment_date', '<=', Carbon::now())
                      ->orWhereNull('enrollment_date');
            })
            ->get();
            
        // Process regular course enrollments
        $regularEnrollments = $activeEnrollments->filter(function($enrollment) {
            return $enrollment->course_id !== null;
        });
        
        $summaryRegularData = [];
        foreach ($regularEnrollments as $enrollment) {
            $courseId = $enrollment->course_id;
            
            if (!isset($summaryRegularData[$courseId])) {
                $summaryRegularData[$courseId] = [
                    'matiere' => $enrollment->course->matiere,
                    'niveau_scolaire' => $enrollment->course->niveau_scolaire,
                    'prix' => $enrollment->course->prix,
                    'total_students' => 0,
                    'revenue' => 0,
                    'course_type' => 'regular'
                ];
            }
            
            // Calculate revenue using monthly amount
            $startDate = Carbon::now()->startOfMonth();
            $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
            $monthlyRevenue = 0;
            
            if ($endDate && $startDate->lte($endDate)) {
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $monthlyRevenue += $enrollment->getRevenueForMonth($currentDate);
                    $currentDate->addMonth();
                }
            } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                $monthlyRevenue += $enrollment->getRevenueForMonth(Carbon::now());
            }
            
            $summaryRegularData[$courseId]['revenue'] += $monthlyRevenue;
            $summaryRegularData[$courseId]['total_students'] += $enrollment->student->student_count;
        }
        
        // Process communication course enrollments
        $commEnrollments = $activeEnrollments->filter(function($enrollment) {
            return $enrollment->communication_course_id !== null;
        });
        
        $summaryCommData = [];
        foreach ($commEnrollments as $enrollment) {
            $courseId = $enrollment->communication_course_id;
            
            if (!isset($summaryCommData[$courseId])) {
                $summaryCommData[$courseId] = [
                    'matiere' => $enrollment->communicationCourse->matiere,
                    'niveau_scolaire' => $enrollment->communicationCourse->niveau_scolaire,
                    'prix' => $enrollment->communicationCourse->prix,
                    'total_students' => 0,
                    'revenue' => 0,
                    'course_type' => 'communication'
                ];
            }
            
            // Calculate revenue using monthly amount
            $startDate = Carbon::now()->startOfMonth();
            $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
            $monthlyRevenue = 0;
            
            if ($endDate && $startDate->lte($endDate)) {
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $monthlyRevenue += $enrollment->getRevenueForMonth($currentDate);
                    $currentDate->addMonth();
                }
            } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                $monthlyRevenue += $enrollment->getRevenueForMonth(Carbon::now());
            }
            
            $summaryCommData[$courseId]['revenue'] += $monthlyRevenue;
            $summaryCommData[$courseId]['total_students'] += $enrollment->student->student_count;
        }
        
        // Convert arrays to objects for the view
        $summaryRegular = collect();
        foreach (array_values($summaryRegularData) as $item) {
            $summaryRegular->push((object)$item);
        }
        
        $summaryComm = collect();
        foreach (array_values($summaryCommData) as $item) {
            $summaryComm->push((object)$item);
        }
        
        // Combine results
        $enrollments = $summaryRegular->concat($summaryComm);
        
        // Get translations for the level
        $levelName = '';
        switch ($niveau_scolaire) {
            case 'premiere_school':
                $levelName = __('Première School');
                break;
            case '1ac':
                $levelName = __('1st Middle School');
                break;
            case '2ac':
                $levelName = __('2nd Middle School');
                break;
            case '3ac':
                $levelName = __('3AC');
                break;
            case 'high_school':
                $levelName = __('High School');
                break;
        }
        
        // Calculate totals
        $totalStudents = $enrollments->sum('total_students');
        $totalRevenue = $enrollments->sum('revenue');
        
        return view('enrollments.summary', compact(
            'enrollments',
            'niveau_scolaire',
            'levelName',
            'totalStudents',
            'totalRevenue'
        ));
    }
    
    /**
     * Display summary by school level
     */
    public function summaryByLevel($level)
    {
        $schoolLevels = [
            'premiere_school' => 'Première École',
            '1ac' => '1ère Année Collège',
            '2ac' => '2ème Année Collège',
            '3ac' => '3éme Année Collège',
            'high_school' => 'Lycée'
        ];
        
        if (!array_key_exists($level, $schoolLevels)) {
            abort(404, 'School level not found');
        }
        
        $levelName = $schoolLevels[$level];
        
        // Get active enrollments for this level
        $activeEnrollments = StudentCourse::with(['student', 'course', 'communicationCourse'])
            ->whereHas('student', function($query) use ($level) {
                $query->where('niveau_scolaire', $level)
                      ->where('status', 'active');
            })
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('enrollment_date', '<=', Carbon::now())
                      ->orWhereNull('enrollment_date');
            })
            ->get();
            
        // Process regular course enrollments
        $regularEnrollments = $activeEnrollments->filter(function($enrollment) {
            return $enrollment->course_id !== null;
        });
        
        $summaryRegularData = [];
        foreach ($regularEnrollments as $enrollment) {
            $courseId = $enrollment->course_id;
            
            if (!isset($summaryRegularData[$courseId])) {
                $summaryRegularData[$courseId] = [
                    'matiere' => $enrollment->course->matiere,
                    'niveau_scolaire' => $enrollment->course->niveau_scolaire,
                    'prix' => $enrollment->course->prix,
                    'total_students' => 0,
                    'revenue' => 0,
                    'course_type' => 'regular'
                ];
            }
            
            // Calculate revenue using monthly amount
            $startDate = Carbon::now()->startOfMonth();
            $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
            $monthlyRevenue = 0;
            
            if ($endDate && $startDate->lte($endDate)) {
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $monthlyRevenue += $enrollment->getRevenueForMonth($currentDate);
                    $currentDate->addMonth();
                }
            } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                $monthlyRevenue += $enrollment->getRevenueForMonth(Carbon::now());
            }
            
            $summaryRegularData[$courseId]['revenue'] += $monthlyRevenue;
            $summaryRegularData[$courseId]['total_students'] += $enrollment->student->student_count;
        }
        
        // Process communication course enrollments
        $commEnrollments = $activeEnrollments->filter(function($enrollment) {
            return $enrollment->communication_course_id !== null;
        });
        
        $summaryCommData = [];
        foreach ($commEnrollments as $enrollment) {
            $courseId = $enrollment->communication_course_id;
            
            if (!isset($summaryCommData[$courseId])) {
                $summaryCommData[$courseId] = [
                    'matiere' => $enrollment->communicationCourse->matiere,
                    'niveau_scolaire' => $enrollment->communicationCourse->niveau_scolaire,
                    'prix' => $enrollment->communicationCourse->prix,
                    'total_students' => 0,
                    'revenue' => 0,
                    'course_type' => 'communication'
                ];
            }
            
            // Calculate revenue using monthly amount
            $startDate = Carbon::now()->startOfMonth();
            $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
            $monthlyRevenue = 0;
            
            if ($endDate && $startDate->lte($endDate)) {
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $monthlyRevenue += $enrollment->getRevenueForMonth($currentDate);
                    $currentDate->addMonth();
                }
            } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                $monthlyRevenue += $enrollment->getRevenueForMonth(Carbon::now());
            }
            
            $summaryCommData[$courseId]['revenue'] += $monthlyRevenue;
            $summaryCommData[$courseId]['total_students'] += $enrollment->student->student_count;
        }
        
        // Convert arrays to objects for the view
        $summaryRegular = collect();
        foreach (array_values($summaryRegularData) as $item) {
            $summaryRegular->push((object)$item);
        }
        
        $summaryComm = collect();
        foreach (array_values($summaryCommData) as $item) {
            $summaryComm->push((object)$item);
        }
        
        // Combine results
        $enrollments = $summaryRegular->concat($summaryComm);
        
        // Calculate total revenue
        $totalRevenue = $enrollments->sum('revenue');
        
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

    /**
     * Update a student course enrollment
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'enrollment_date' => 'required|date',
            'payment_expiry' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'months' => 'required|integer|min:1',
            'monthly_revenue_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        
        try {
            $enrollment = StudentCourse::findOrFail($id);
            
            // Log dates before update
            \Log::info("Before updating enrollment {$id}", [
                'enrollment_date' => $request->enrollment_date,
                'payment_expiry' => $request->payment_expiry
            ]);
            
            // Format dates for direct insertion to avoid mutator issues
            $enrollmentDate = Carbon::parse($request->enrollment_date)->format('Y-m-d');
            $paymentExpiry = Carbon::parse($request->payment_expiry)->format('Y-m-d');
            
            // Update directly in DB to bypass any mutator issues
            DB::table('student_courses')
                ->where('id', $id)
                ->update([
                    'enrollment_date' => $enrollmentDate,
                    'payment_expiry' => $paymentExpiry,
                    'paid_amount' => $validated['paid_amount'],
                    'months' => $validated['months'],
                    'monthly_revenue_amount' => $validated['monthly_revenue_amount'] ?: null,
                    'status' => $validated['status'],
                    'updated_at' => now()
                ]);
            
            // Reload the enrollment to get fresh data
            $enrollment = StudentCourse::findOrFail($id);
            
            // Log after update
            \Log::info("After updating enrollment {$id}", [
                'enrollment_date' => $enrollment->enrollment_date,
                'payment_expiry' => $enrollment->payment_expiry
            ]);
            
            return redirect()->route('student-courses.index')
                ->with('success', 'Enrollment updated successfully!');
        } catch (\Exception $e) {
            \Log::error("Error updating enrollment {$id}: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['date_error' => 'Error updating enrollment: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a student course enrollment
     */
    public function destroy($id)
    {
        $enrollment = StudentCourse::findOrFail($id);
        $enrollment->delete();
        
        return redirect()->route('student-courses.index')
            ->with('success', 'Enrollment deleted successfully!');
    }

    /**
     * Display enrollments that are expiring soon
     */
    public function nearExpiry()
    {
        $today = Carbon::today();
        $twoWeeksFromNow = Carbon::today()->addDays(14);
        
        $nearExpiryEnrollments = StudentCourse::with(['student', 'course', 'communicationCourse'])
            ->where('status', 'active')
            ->whereBetween('payment_expiry', [$today, $twoWeeksFromNow])
            ->orderBy('payment_expiry')
            ->paginate(15);
        
        return view('student-courses.near-expiry', compact('nearExpiryEnrollments'));
    }

    /**
     * Display index of student courses 
     */
    public function studentCoursesIndex(Request $request)
    {
        $query = StudentCourse::with(['student', 'course', 'communicationCourse']);
        
        // Apply search if set
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhere('phone', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Apply status filter if set
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply course type filter if set
        if ($request->filled('course_type')) {
            if ($request->course_type === 'regular') {
                $query->whereNotNull('course_id');
            } elseif ($request->course_type === 'communication') {
                $query->whereNotNull('communication_course_id');
            }
        }
        
        // Get paginated results
        $enrollments = $query->orderBy('payment_expiry', 'asc')
                             ->paginate(15);
        
        return view('student-courses.manage', compact('enrollments'));
    }

    /**
     * Display the form for creating a new student course
     */
    public function create()
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $regularCourses = Cours::where('type', 'regular')->orderBy('niveau_scolaire')->orderBy('matiere')->get();
        $communicationCourses = CommunicationCourse::orderBy('niveau_scolaire')->orderBy('matiere')->get();
        
        return view('student-courses.create', compact('students', 'regularCourses', 'communicationCourses'));
    }

    /**
     * Display the form for editing a student course
     */
    public function edit($id)
    {
        $enrollment = StudentCourse::with(['student', 'course', 'communicationCourse'])->findOrFail($id);
        return view('student-courses.edit', compact('enrollment'));
    }

    /**
     * Store a new student course enrollment
     */
    public function storeStudentCourse(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_type' => 'required|in:regular,communication',
            'course_id' => 'required_if:course_type,regular|nullable|exists:cours,id',
            'communication_course_id' => 'required_if:course_type,communication|nullable|exists:communication_courses,id',
            'enrollment_date' => 'required|date',
            'months' => 'required|integer|min:1',
            'paid_amount' => 'required|numeric|min:0',
            'monthly_revenue_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        
        try {
            $enrollment = new StudentCourse();
            $enrollment->student_id = $validated['student_id'];
            
            // Set course information based on type
            if ($validated['course_type'] === 'regular') {
                $enrollment->course_id = $validated['course_id'];
            } else {
                $enrollment->communication_course_id = $validated['communication_course_id'];
            }
            
            // Parse and set dates
            $enrollmentDate = Carbon::createFromFormat('Y-m-d', $validated['enrollment_date']);
            $enrollment->enrollment_date = $enrollmentDate;
            
            // Calculate payment expiry date based on enrollment date and months
            $enrollment->payment_expiry = $enrollmentDate->copy()->addMonths($validated['months']);
            
            // Set other fields
            $enrollment->months = $validated['months'];
            $enrollment->paid_amount = $validated['paid_amount'];
            $enrollment->monthly_revenue_amount = $validated['monthly_revenue_amount'] ?: null;
            $enrollment->status = $validated['status'];
            
            $enrollment->save();
            
            return redirect()->route('student-courses.index')
                ->with('success', 'Enrollment created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error creating enrollment: ' . $e->getMessage()]);
        }
    }
} 