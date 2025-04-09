<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Cours;
use App\Models\CommunicationCourse;
use App\Models\Enrollment;
use App\Models\StudentCourse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class ReportController extends Controller
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
     * Display the revenue reports dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $selectedLevel = $request->input('niveau_scolaire', '');
        
        // Get total revenue (excluding future enrollments)
        $totalRevenue = 0;
        $activeEnrollmentsQuery = StudentCourse::where('status', 'active')
            ->where(function($query) {
                $query->where('enrollment_date', '<=', Carbon::now())
                      ->orWhereNull('enrollment_date');
            });
            
        // Apply level filter if selected
        if (!empty($selectedLevel)) {
            $activeEnrollmentsQuery->whereHas('student', function($query) use ($selectedLevel) {
                $query->where('niveau_scolaire', $selectedLevel);
            });
        }
        
        $activeEnrollments = $activeEnrollmentsQuery->get();
            
        foreach ($activeEnrollments as $enrollment) {
            // Get the remaining months of enrollment from now
            $now = Carbon::now()->startOfMonth();
            $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
            
            if ($endDate && $now->lte($endDate)) {
                // Get monthly revenue for all remaining months
                $currentMonth = $now->copy();
                while ($currentMonth->lte($endDate)) {
                    $totalRevenue += $enrollment->getRevenueForMonth($currentMonth);
                    $currentMonth->addMonth();
                }
            } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                // If payment is still valid but within the current month
                $totalRevenue += $enrollment->getRevenueForMonth(Carbon::now());
            }
        }
        
        // Get current month revenue
        $currentMonth = Carbon::now();
        $currentMonthRevenue = 0;
        
        foreach ($activeEnrollments as $enrollment) {
            $currentMonthRevenue += $enrollment->getRevenueForMonth($currentMonth);
        }
        
        // Get previous month revenue
        $previousMonth = Carbon::now()->subMonth();
        $previousMonthRevenue = 0;
        
        foreach ($activeEnrollments as $enrollment) {
            $previousMonthRevenue += $enrollment->getRevenueForMonth($previousMonth);
        }
        
        // Get revenue by month for the last 6 months with level filter
        $revenueByMonth = $this->getRevenueByMonth(6, $selectedLevel);
        
        // Get revenue by subject with level filter
        $revenueBySubject = empty($selectedLevel) 
            ? $this->getRevenueBySubject() 
            : $this->getRevenueBySubjectAndLevel($selectedLevel);
        
        // Get revenue by level
        $revenueByLevel = $this->getRevenueByLevel();
        
        return view('reports.index', compact(
            'totalRevenue',
            'currentMonthRevenue',
            'previousMonthRevenue',
            'revenueByMonth',
            'revenueBySubject',
            'revenueByLevel',
            'selectedLevel'
        ));
    }

    /**
     * Get revenue by month for the last X months.
     *
     * @param int $months
     * @param string $level Optional level filter
     * @return array
     */
    private function getRevenueByMonth($months = 6, $level = '')
    {
        $result = [];
        
        for ($i = 0; $i < $months; $i++) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('F Y');
            
            // Get active enrollments
            $query = StudentCourse::where('status', 'active')
                ->where(function($query) use ($date) {
                    $query->where('enrollment_date', '<=', $date->endOfMonth())
                          ->orWhereNull('enrollment_date');
                });
            
            // Apply level filter if specified
            if (!empty($level)) {
                $query->whereHas('student', function($query) use ($level) {
                    $query->where('niveau_scolaire', $level);
                });
            }
            
            $activeEnrollments = $query->get();
                
            // Calculate revenue for this month using monthly revenue amount
            $monthlyRevenue = 0;
            foreach ($activeEnrollments as $enrollment) {
                $monthlyRevenue += $enrollment->getRevenueForMonth($date);
            }
                
            $result[] = [
                'month' => $monthName,
                'revenue' => $monthlyRevenue
            ];
        }
        
        return array_reverse($result);
    }

    /**
     * Get revenue by subject.
     *
     * @return array
     */
    private function getRevenueBySubject()
    {
        $subjects = [];
        $activeEnrollments = StudentCourse::with(['student', 'course', 'communicationCourse'])
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('enrollment_date', '<=', Carbon::now())
                      ->orWhereNull('enrollment_date');
            })
            ->get();
            
        // Get all unique course names and create subjects list
        foreach ($activeEnrollments as $enrollment) {
            $courseName = $enrollment->getCourseName();
            if (!empty($courseName) && $courseName !== 'N/A' && !in_array($courseName, $subjects)) {
                $subjects[] = $courseName;
            }
        }
        
        $result = [];
        
        foreach ($subjects as $subject) {
            $subjectEnrollments = $activeEnrollments->filter(function($enrollment) use ($subject) {
                return $enrollment->getCourseName() === $subject;
            });
            
            $subjectRevenue = 0;
            $subjectStudents = 0;
            
            foreach ($subjectEnrollments as $enrollment) {
                // Calculate remaining months of revenue
                $startDate = Carbon::now()->startOfMonth();
                $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
                
                if ($endDate && $startDate->lte($endDate)) {
                    $currentDate = $startDate->copy();
                    while ($currentDate->lte($endDate)) {
                        $subjectRevenue += $enrollment->getRevenueForMonth($currentDate);
                        $currentDate->addMonth();
                    }
                } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                    // If payment is still valid but within the current month
                    $subjectRevenue += $enrollment->getRevenueForMonth(Carbon::now());
                }
                
                $subjectStudents += $enrollment->student->student_count;
            }
                
            $result[] = [
                'subject' => $subject,
                'total_revenue' => $subjectRevenue,
                'total_students' => $subjectStudents
            ];
        }
        
        // Sort by revenue in descending order
        usort($result, function($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });
        
        return $result;
    }

    /**
     * Get revenue by level.
     *
     * @return array
     */
    private function getRevenueByLevel()
    {
        $levels = $this->getLevelLabels();
        
        $activeEnrollments = StudentCourse::with('student')
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('enrollment_date', '<=', Carbon::now())
                      ->orWhereNull('enrollment_date');
            })
            ->get();
        
        $result = [];
        
        foreach ($levels as $key => $label) {
            // Get enrollments for this level
            $levelEnrollments = $activeEnrollments->filter(function($enrollment) use ($key) {
                return $enrollment->student->niveau_scolaire === $key;
            });
            
            // Calculate level revenue
            $levelRevenue = 0;
            foreach ($levelEnrollments as $enrollment) {
                // Calculate remaining months of revenue
                $startDate = Carbon::now()->startOfMonth();
                $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
                
                if ($endDate && $startDate->lte($endDate)) {
                    $currentDate = $startDate->copy();
                    while ($currentDate->lte($endDate)) {
                        $levelRevenue += $enrollment->getRevenueForMonth($currentDate);
                        $currentDate->addMonth();
                    }
                } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                    // If payment is still valid but within the current month
                    $levelRevenue += $enrollment->getRevenueForMonth(Carbon::now());
                }
            }
            
            // Count students for this level
            $studentIds = $levelEnrollments->pluck('student_id')->unique();
            $studentCount = 0;
            
            if ($studentIds->count() > 0) {
                $studentCount = Student::whereIn('id', $studentIds->toArray())
                    ->where('niveau_scolaire', $key)
                    ->sum('student_count');
            }
                
            $result[] = [
                'level' => $label,
                'revenue' => $levelRevenue,
                'count' => $studentCount
            ];
        }
        
        return $result;
    }

    /**
     * Show monthly revenue report.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function monthlyRevenue(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($year, $month, 1);
            $monthKey = $date->format('Y-m');
            $endOfMonth = $date->copy()->endOfMonth();
            
            // Only count revenue for students whose enrollment has started by the end of this month
            $monthRevenueCriteria = Carbon::now()->gt($endOfMonth) 
                ? $endOfMonth // For past months, use end of month as cutoff
                : Carbon::now(); // For current or future months, use today as cutoff
                
            // Get all active enrollments for this month
            $enrollments = StudentCourse::where('status', 'active')
                ->where(function($query) use ($monthRevenueCriteria) {
                    $query->where('enrollment_date', '<=', $monthRevenueCriteria)
                          ->orWhereNull('enrollment_date');
                })
                ->get();
                
            // Filter enrollments to only those that contribute revenue for this specific month
            $selectedDate = Carbon::createFromDate($year, $month, 1);
            $monthlyEnrollments = $enrollments->filter(function ($enrollment) use ($selectedDate) {
                return $enrollment->getRevenueForMonth($selectedDate) > 0;
            });
            
            // Calculate the revenue for this month using the monthly revenue amount
            $monthlyRevenue = $monthlyEnrollments->sum(function ($enrollment) use ($selectedDate) {
                return $enrollment->getRevenueForMonth($selectedDate);
            });
            
            // Count distinct students with active enrollments for this month
            $studentIds = $monthlyEnrollments->pluck('student_id')->unique();
            $studentsCount = 0;
            
            if ($studentIds->count() > 0) {
                $studentsCount = Student::whereIn('id', $studentIds)
                    ->sum('student_count');
            }
            
            $monthlyData[] = [
                'month' => $date->format('F'),
                'revenue' => $monthlyRevenue,
                'count' => $studentsCount
            ];
        }
        
        // Get available years for the dropdown
        $years = StudentCourse::selectRaw('YEAR(enrollment_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [Carbon::now()->year];
        }
        
        return view('reports.monthly', compact('monthlyData', 'years', 'year'));
    }

    /**
     * Show subject revenue report.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function subjectRevenue(Request $request)
    {
        $selectedLevel = $request->input('level', 'all');
        
        // Get all school levels for the dropdown
        $levels = [
            'all' => 'All Levels',
            'premiere_school' => 'Première School',
            '1ac' => '1st Middle School',
            '2ac' => '2nd Middle School',
            '3ac' => '3AC',
            'tronc_commun' => 'Tronc Commun',
            'deuxieme_annee' => 'Deuxième Année',
            'bac' => 'Bac'
        ];
        
        $revenueBySubject = $this->getRevenueBySubjectAndLevel($selectedLevel);
        
        return view('reports.subjects', compact('revenueBySubject', 'levels', 'selectedLevel'));
    }

    /**
     * Get revenue by subject, filtered by level if specified.
     *
     * @param string $level Level to filter by, or 'all' for all levels
     * @return array
     */
    private function getRevenueBySubjectAndLevel($level = 'all')
    {
        $subjects = [];
        
        // Query active enrollments
        $query = StudentCourse::with(['student', 'course', 'communicationCourse'])
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('enrollment_date', '<=', Carbon::now())
                      ->orWhereNull('enrollment_date');
            });
            
        // Apply level filter if not 'all'
        if ($level !== 'all') {
            $query->whereHas('student', function ($q) use ($level) {
                $q->where('niveau_scolaire', $level);
            });
        }
        
        $activeEnrollments = $query->get();
            
        // Get all unique course names and create subjects list
        foreach ($activeEnrollments as $enrollment) {
            $courseName = $enrollment->getCourseName();
            if (!empty($courseName) && $courseName !== 'N/A' && !in_array($courseName, $subjects)) {
                $subjects[] = $courseName;
            }
        }
        
        $result = [];
        
        foreach ($subjects as $subject) {
            $subjectEnrollments = $activeEnrollments->filter(function($enrollment) use ($subject) {
                return $enrollment->getCourseName() === $subject;
            });
            
            // Group by level
            $levelBreakdown = [];
            $subjectTotalRevenue = 0;
            $subjectTotalStudents = 0;
            
            foreach ($subjectEnrollments as $enrollment) {
                $studentLevel = $enrollment->student->niveau_scolaire;
                
                // Calculate remaining months of revenue
                $startDate = Carbon::now()->startOfMonth();
                $endDate = $enrollment->payment_expiry ? $enrollment->payment_expiry->startOfMonth() : null;
                $enrollmentRevenue = 0;
                
                if ($endDate && $startDate->lte($endDate)) {
                    $currentDate = $startDate->copy();
                    while ($currentDate->lte($endDate)) {
                        $enrollmentRevenue += $enrollment->getRevenueForMonth($currentDate);
                        $currentDate->addMonth();
                    }
                } else if ($enrollment->payment_expiry && $enrollment->payment_expiry->gte(Carbon::now())) {
                    // If payment is still valid but within the current month
                    $enrollmentRevenue += $enrollment->getRevenueForMonth(Carbon::now());
                }
                
                if (!isset($levelBreakdown[$studentLevel])) {
                    $levelBreakdown[$studentLevel] = [
                        'revenue' => 0,
                        'students' => 0
                    ];
                }
                
                $levelBreakdown[$studentLevel]['revenue'] += $enrollmentRevenue;
                $levelBreakdown[$studentLevel]['students'] += $enrollment->student->student_count;
                
                $subjectTotalRevenue += $enrollmentRevenue;
                $subjectTotalStudents += $enrollment->student->student_count;
            }
                
            $result[] = [
                'subject' => $subject,
                'total_revenue' => $subjectTotalRevenue,
                'total_students' => $subjectTotalStudents,
                'level_breakdown' => $levelBreakdown
            ];
        }
        
        // Sort by revenue in descending order
        usort($result, function($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });
        
        return $result;
    }

    /**
     * Show level revenue report.
     *
     * @return \Illuminate\Http\Response
     */
    public function levelRevenue()
    {
        $revenueByLevel = $this->getRevenueByLevel();
        $levelLabels = $this->getLevelLabels();
        
        // Flip array to help find keys by values for export links
        $flippedLevelLabels = array_flip($levelLabels);
        
        return view('reports.levels', compact('revenueByLevel', 'levelLabels', 'flippedLevelLabels'));
    }
    
    /**
     * Get standardized level labels.
     *
     * @return array
     */
    private function getLevelLabels()
    {
        return [
            'premiere_school' => 'Première School',
            '1ac' => '1st Middle School',
            '2ac' => '2nd Middle School',
            '3ac' => '3AC',
            'tronc_commun' => 'Tronc Commun',
            'deuxieme_annee' => 'Deuxième Année',
            'bac' => 'Bac'
        ];
    }

    /**
     * Generate an export of student data.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportStudents(Request $request)
    {
        $query = Student::with(['course', 'communicationCourse', 'enrollments.course', 'enrollments.communicationCourse']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('niveau_scolaire')) {
            $query->where('niveau_scolaire', $request->niveau_scolaire);
        }
        
        $students = $query->latest()->get();
        
        $filename = 'students_export_';
        if ($request->filled('niveau_scolaire')) {
            $filename .= $request->niveau_scolaire . '_';
        }
        $filename .= date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Parent Name', 'Level', 
                'Courses', 'Status', 'Paid Amount', 'Monthly Revenue', 'Payment Expiry', 
                'Enrollment Date', 'Months'
            ]);
            
            // Define level labels
            $levelLabels = [
                'premiere_school' => 'Première School',
                '1ac' => '1st Middle School',
                '2ac' => '2nd Middle School',
                '3ac' => '3AC',
                'tronc_commun' => 'Tronc Commun',
                'deuxieme_annee' => 'Deuxième Année',
                'bac' => 'Bac'
            ];
            
            // Add data rows
            foreach ($students as $student) {
                $level = $levelLabels[$student->niveau_scolaire] ?? $student->niveau_scolaire;
                
                // Get all courses for this student
                $courses = [];
                $monthlyRevenue = 0;
                
                foreach ($student->enrollments as $enrollment) {
                    $course = $enrollment->course ?? $enrollment->communicationCourse;
                    if ($course) {
                        $courses[] = $course->matiere;
                        $monthlyRevenue += $enrollment->monthly_revenue_amount ?? $course->prix;
                    }
                }
                
                fputcsv($file, [
                    $student->id,
                    $student->name,
                    $student->email,
                    $student->phone,
                    $student->parent_name,
                    $level,
                    implode(', ', $courses),
                    $student->status,
                    $student->paid_amount,
                    $monthlyRevenue,
                    $student->payment_expiry ? $student->payment_expiry->format('Y-m-d') : '',
                    $student->enrollment_date ? $student->enrollment_date->format('Y-m-d') : '',
                    $student->months
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate a PDF export of student data.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        // Get selected language from request
        $pdfLanguage = $request->input('pdf_language', app()->getLocale());
        
        // Temporarily switch to selected language for PDF generation
        $originalLocale = app()->getLocale();
        app()->setLocale($pdfLanguage);
        
        $selectedLevel = $request->input('niveau_scolaire', '');
        $query = Student::with(['course', 'communicationCourse', 'enrollments.course', 'enrollments.communicationCourse']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if (!empty($selectedLevel)) {
            $query->where('niveau_scolaire', $selectedLevel);
        }
        
        $students = $query->latest()->get();
        
        // Calculate current monthly revenue for each student
        foreach ($students as $student) {
            $student->current_monthly_revenue = 0;
            $student->courses_list = [];
            $currentMonth = Carbon::now()->startOfMonth();
            
            foreach ($student->enrollments as $enrollment) {
                $student->current_monthly_revenue += $enrollment->getRevenueForMonth($currentMonth);
                
                // Add course to the list
                $course = $enrollment->course ?? $enrollment->communicationCourse;
                if ($course) {
                    $coursesArray = $student->courses_list;
                    $coursesArray[] = $course->matiere;
                    $student->courses_list = $coursesArray;
                }
            }
        }
        
        $levelLabels = $this->getLevelLabels();
        
        $filename = 'students_export_';
        if (!empty($selectedLevel)) {
            $filename .= $selectedLevel . '_';
        }
        $filename .= date('Y-m-d') . '.pdf';
        
        // Configure PDF options
        $options = [
            'default_font' => 'DejaVu Sans'
        ];
        
        // Set RTL mode if Arabic is the selected language
        if ($pdfLanguage === 'ar') {
            $options['isRtl'] = true;
        }
        
        // Create PDF with options
        $pdf = PDF::loadView('reports.export-pdf', [
            'students' => $students,
            'levelLabels' => $levelLabels,
            'selectedLevel' => $selectedLevel,
            'date' => Carbon::now()->format('Y-m-d')
        ]);
        
        // Apply RTL and other configurations for Arabic
        if ($pdfLanguage === 'ar') {
            $pdf->setOption('isRtl', true);
            $pdf->setOption('defaultFont', 'DejaVu Sans');
            $pdf->setPaper('a4', 'landscape');
        }
        
        // Restore original locale
        app()->setLocale($originalLocale);
        
        return $pdf->download($filename);
    }

    /**
     * Export revenue data by subject and level to CSV.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportSubjectRevenue(Request $request)
    {
        // Get selected language from request
        $pdfLanguage = $request->input('pdf_language', app()->getLocale());
        
        // Temporarily switch to selected language for export generation
        $originalLocale = app()->getLocale();
        app()->setLocale($pdfLanguage);
        
        $selectedLevel = $request->input('level', 'all');
        $revenueBySubject = $this->getRevenueBySubjectAndLevel($selectedLevel);
        
        $filename = 'subject_revenue_';
        if ($selectedLevel !== 'all') {
            $filename .= $selectedLevel . '_';
        }
        $filename .= date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($revenueBySubject, $selectedLevel, $pdfLanguage) {
            $file = fopen('php://output', 'w');
            
            // Define level labels
            $levelLabels = [
                'premiere_school' => $pdfLanguage === 'ar' ? 'المدرسة الابتدائية' : ($pdfLanguage === 'fr' ? 'Première School' : 'Primary School'),
                '1ac' => $pdfLanguage === 'ar' ? 'السنة الأولى إعدادي' : ($pdfLanguage === 'fr' ? '1ère Année Collège' : '1st Middle School'),
                '2ac' => $pdfLanguage === 'ar' ? 'السنة الثانية إعدادي' : ($pdfLanguage === 'fr' ? '2ème Année Collège' : '2nd Middle School'),
                '3ac' => $pdfLanguage === 'ar' ? 'السنة الثالثة إعدادي' : ($pdfLanguage === 'fr' ? '3ème Année Collège' : '3AC'),
                'tronc_commun' => $pdfLanguage === 'ar' ? 'الجذع المشترك' : ($pdfLanguage === 'fr' ? 'Tronc Commun' : 'Common Core'),
                'deuxieme_annee' => $pdfLanguage === 'ar' ? 'السنة الثانية باكالوريا' : ($pdfLanguage === 'fr' ? 'Deuxième Année' : 'Second Year'),
                'bac' => $pdfLanguage === 'ar' ? 'باكالوريا' : ($pdfLanguage === 'fr' ? 'Baccalauréat' : 'Baccalaureate')
            ];
            
            // Add CSV headers based on language
            if ($selectedLevel === 'all') {
                if ($pdfLanguage === 'ar') {
                    fputcsv($file, ['المادة', 'إجمالي الإيرادات', 'إجمالي الطلاب', 'تفصيل المستويات']);
                } elseif ($pdfLanguage === 'fr') {
                    fputcsv($file, ['Matière', 'Revenu Total', 'Total des Étudiants', 'Détail par Niveau']);
                } else {
                    fputcsv($file, ['Subject', 'Total Revenue', 'Total Students', 'Level Breakdown']);
                }
            } else {
                if ($pdfLanguage === 'ar') {
                    fputcsv($file, ['المادة', 'إجمالي الإيرادات', 'إجمالي الطلاب']);
                } elseif ($pdfLanguage === 'fr') {
                    fputcsv($file, ['Matière', 'Revenu Total', 'Total des Étudiants']);
                } else {
                    fputcsv($file, ['Subject', 'Total Revenue', 'Total Students']);
                }
            }
            
            // Add data rows
            foreach ($revenueBySubject as $subject) {
                if ($selectedLevel === 'all') {
                    // Main row for subject
                    fputcsv($file, [
                        $subject['subject'],
                        number_format($subject['total_revenue'], 2),
                        $subject['total_students'],
                        ''
                    ]);
                    
                    // Sub-rows for each level
                    foreach ($subject['level_breakdown'] as $levelKey => $levelData) {
                        $levelName = $levelLabels[$levelKey] ?? $levelKey;
                        fputcsv($file, [
                            ' - ' . $levelName,
                            number_format($levelData['revenue'], 2),
                            $levelData['students'],
                            ''
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        $subject['subject'],
                        number_format($subject['total_revenue'], 2),
                        $subject['total_students']
                    ]);
                }
            }
            
            // Add total row
            $totalRevenue = array_sum(array_column($revenueBySubject, 'total_revenue'));
            $totalStudents = array_sum(array_column($revenueBySubject, 'total_students'));
            
            $totalLabel = $pdfLanguage === 'ar' ? 'المجموع' : ($pdfLanguage === 'fr' ? 'TOTAL' : 'TOTAL');
            
            if ($selectedLevel === 'all') {
                fputcsv($file, [$totalLabel, number_format($totalRevenue, 2), $totalStudents, '']);
            } else {
                fputcsv($file, [$totalLabel, number_format($totalRevenue, 2), $totalStudents]);
            }
            
            fclose($file);
        };
        
        // Restore original locale
        app()->setLocale($originalLocale);
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate a PDF report of revenue by month and level.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportRevenueByMonthAndLevel(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $monthlyData = [];
        
        // Define school levels
        $levels = [
            'premiere_school' => 'Première School',
            '1ac' => '1st Middle School',
            '2ac' => '2nd Middle School',
            '3ac' => '3AC',
            'high_school' => 'High School'
        ];
        
        // Initialize level data
        $levelData = [];
        foreach ($levels as $key => $label) {
            $levelData[$key] = [
                'label' => $label,
                'months' => [],
                'total_revenue' => 0,
                'total_students' => 0
            ];
        }
        
        // Get data for each month
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($year, $month, 1);
            $monthKey = $date->format('Y-m');
            $monthName = $date->format('F');
            $endOfMonth = $date->copy()->endOfMonth();
            
            // Only count revenue for students whose enrollment has started by the end of this month
            $monthRevenueCriteria = Carbon::now()->gt($endOfMonth) 
                ? $endOfMonth // For past months, use end of month as cutoff
                : Carbon::now(); // For current or future months, use today as cutoff
            
            // Get all active enrollments for this month
            $activeEnrollments = StudentCourse::with('student')
                ->where('status', 'active')
                ->where(function($query) use ($monthRevenueCriteria) {
                    $query->where('enrollment_date', '<=', $monthRevenueCriteria)
                          ->orWhereNull('enrollment_date');
                })
                ->get();
                
            // Filter enrollments to only those that have revenue for this month
            $selectedDate = Carbon::createFromDate($year, $month, 1);
            $monthlyEnrollments = collect();
            
            foreach ($activeEnrollments as $enrollment) {
                $monthlyRevenue = $enrollment->getRevenueForMonth($selectedDate);
                if ($monthlyRevenue > 0) {
                    $enrollment->monthly_amount = $monthlyRevenue;
                    $monthlyEnrollments->push($enrollment);
                }
            }
            
            // Calculate total monthly revenue
            $monthRevenue = $monthlyEnrollments->sum('monthly_amount');
            
            // Get students with active enrollments for this month
            $studentIds = $monthlyEnrollments->pluck('student_id')->unique();
            $monthStudents = 0;
            
            if ($studentIds->count() > 0) {
                $monthStudents = Student::whereIn('id', $studentIds->toArray())
                    ->sum('student_count');
            }
            
            $monthlyData[] = [
                'month' => $monthName,
                'revenue' => $monthRevenue,
                'count' => $monthStudents
            ];
            
            // Process data by educational level
            foreach ($levels as $key => $label) {
                // Get enrollments for this level in this month
                $levelEnrollments = $monthlyEnrollments->filter(function($enrollment) use ($key) {
                    return $enrollment->student->niveau_scolaire === $key;
                });
                
                // Calculate revenue for this level in this month
                $levelRevenue = $levelEnrollments->sum('monthly_amount');
                
                // Count students for this level in this month
                $levelStudentIds = $levelEnrollments->pluck('student_id')->unique();
                $levelStudents = 0;
                
                if ($levelStudentIds->count() > 0) {
                    $levelStudents = Student::whereIn('id', $levelStudentIds->toArray())
                        ->where('niveau_scolaire', $key)
                        ->sum('student_count');
                }
                
                if (!isset($levelData[$key]['months'][$monthName])) {
                    $levelData[$key]['months'][$monthName] = [
                        'revenue' => 0,
                        'count' => 0
                    ];
                }
                
                $levelData[$key]['months'][$monthName]['revenue'] = $levelRevenue;
                $levelData[$key]['months'][$monthName]['count'] = $levelStudents;
            }
        }
        
        $pdf = PDF::loadView('reports.revenue-by-month-level-pdf', [
            'year' => $year,
            'monthlyData' => $monthlyData,
            'levelData' => $levelData,
            'levels' => $levels,
            'date' => Carbon::now()->format('Y-m-d')
        ]);
        
        return $pdf->download('revenue_by_month_and_level_' . $year . '.pdf');
    }

    /**
     * Show revenue by month and level.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function revenueByMonthAndLevel(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $monthlyData = [];
        
        // Define school levels
        $levels = [
            'premiere_school' => __('Première School'),
            '1ac' => __('1st Middle School'),
            '2ac' => __('2nd Middle School'),
            '3ac' => __('3AC'),
            'high_school' => __('High School')
        ];
        
        // Initialize level data
        $levelData = [];
        foreach ($levels as $key => $label) {
            $levelData[$key] = [
                'label' => $label,
                'months' => [],
                'total_revenue' => 0,
                'total_students' => 0
            ];
        }
        
        // Get data for each month
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($year, $month, 1);
            $monthKey = $date->format('Y-m');
            $monthName = $date->format('F');
            $endOfMonth = $date->copy()->endOfMonth();
            
            // Only count revenue for students whose enrollment has started by the end of this month
            $monthRevenueCriteria = Carbon::now()->gt($endOfMonth) 
                ? $endOfMonth // For past months, use end of month as cutoff
                : Carbon::now(); // For current or future months, use today as cutoff
            
            // Get all active enrollments for this month
            $activeEnrollments = StudentCourse::with('student')
                ->where('status', 'active')
                ->where(function($query) use ($monthRevenueCriteria) {
                    $query->where('enrollment_date', '<=', $monthRevenueCriteria)
                          ->orWhereNull('enrollment_date');
                })
                ->get();
                
            // Filter enrollments to only those that have revenue for this month
            $selectedDate = Carbon::createFromDate($year, $month, 1);
            $monthlyEnrollments = collect();
            
            foreach ($activeEnrollments as $enrollment) {
                $monthlyRevenue = $enrollment->getRevenueForMonth($selectedDate);
                if ($monthlyRevenue > 0) {
                    $enrollment->monthly_amount = $monthlyRevenue;
                    $monthlyEnrollments->push($enrollment);
                }
            }
            
            // Calculate total monthly revenue
            $monthRevenue = $monthlyEnrollments->sum('monthly_amount');
            
            // Get students with active enrollments for this month
            $studentIds = $monthlyEnrollments->pluck('student_id')->unique();
            $monthStudents = 0;
            
            if ($studentIds->count() > 0) {
                $monthStudents = Student::whereIn('id', $studentIds->toArray())
                    ->sum('student_count');
            }
            
            $monthlyData[] = [
                'month' => $monthName,
                'revenue' => $monthRevenue,
                'students' => $monthStudents
            ];
            
            // Process data by educational level
            foreach ($levels as $key => $label) {
                // Get enrollments for this level in this month
                $levelEnrollments = $monthlyEnrollments
                    ->filter(function($enrollment) use ($key) {
                        return $enrollment->student->niveau_scolaire === $key;
                    });
                
                // Calculate revenue for this level in this month
                $levelRevenue = $levelEnrollments->sum('monthly_amount');
                
                // Count students for this level in this month
                $levelStudentIds = $levelEnrollments->pluck('student_id')->unique();
                $levelStudents = 0;
                
                if ($levelStudentIds->count() > 0) {
                    $levelStudents = Student::whereIn('id', $levelStudentIds->toArray())
                        ->where('niveau_scolaire', $key)
                        ->sum('student_count');
                }
                
                $levelData[$key]['months'][$month] = [
                    'revenue' => $levelRevenue,
                    'students' => $levelStudents
                ];
                
                $levelData[$key]['total_revenue'] += $levelRevenue;
                $levelData[$key]['total_students'] = isset($levelData[$key]['total_students']) ? 
                    $levelData[$key]['total_students'] + $levelStudents : $levelStudents;
            }
        }
        
        // Get available years for the dropdown
        $years = StudentCourse::selectRaw('YEAR(enrollment_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [Carbon::now()->year];
        }
        
        return view('reports.month-level', compact('monthlyData', 'levelData', 'levels', 'years', 'year'));
    }

    /**
     * Display monthly revenue breakdown for each student enrollment
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function monthlyRevenueBreakdown(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        
        // Create a date for the selected month
        $selectedDate = Carbon::createFromDate($year, $month, 1);
        $monthName = $selectedDate->format('F Y');
        
        // Get all active enrollments
        $enrollments = StudentCourse::with(['student', 'course', 'communicationCourse'])
            ->where('status', 'active')
            ->get();
            
        // Filter enrollments to only those that have revenue in the selected month
        $monthlyEnrollments = $enrollments->filter(function ($enrollment) use ($selectedDate) {
            return $enrollment->getRevenueForMonth($selectedDate) > 0;
        });
        
        // Group enrollments by student
        $studentEnrollments = [];
        $totalMonthlyRevenue = 0;
        
        foreach ($monthlyEnrollments as $enrollment) {
            $studentId = $enrollment->student_id;
            $monthlyRevenue = $enrollment->getRevenueForMonth($selectedDate);
            $totalMonthlyRevenue += $monthlyRevenue;
            
            if (!isset($studentEnrollments[$studentId])) {
                $studentEnrollments[$studentId] = [
                    'student' => $enrollment->student,
                    'enrollments' => [],
                    'total' => 0
                ];
            }
            
            $studentEnrollments[$studentId]['enrollments'][] = [
                'enrollment' => $enrollment,
                'monthly_revenue' => $monthlyRevenue,
                'course_name' => $enrollment->getCourseName(),
                'course_type' => $enrollment->getCourseType(),
            ];
            
            $studentEnrollments[$studentId]['total'] += $monthlyRevenue;
        }
        
        // Get available years and months for the dropdown
        $years = StudentCourse::selectRaw('YEAR(enrollment_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [Carbon::now()->year];
        }
        
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthDate = Carbon::createFromDate($year, $i, 1);
            $months[$i] = $monthDate->format('F');
        }
        
        return view('reports.monthly-breakdown', compact(
            'studentEnrollments',
            'monthName',
            'totalMonthlyRevenue',
            'years',
            'months',
            'year',
            'month'
        ));
    }
} 