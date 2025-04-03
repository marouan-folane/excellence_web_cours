<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Cours;
use App\Models\CommunicationCourse;
use App\Models\Enrollment;
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
    public function index()
    {
        // Get total revenue
        $totalRevenue = Student::sum('paid_amount');
        
        // Get current month revenue
        $currentMonth = Carbon::now()->format('Y-m');
        $currentMonthRevenue = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->sum('paid_amount');
        
        // Get previous month revenue
        $previousMonth = Carbon::now()->subMonth()->format('Y-m');
        $previousMonthRevenue = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$previousMonth])
            ->sum('paid_amount');
        
        // Get revenue by month for the last 6 months
        $revenueByMonth = $this->getRevenueByMonth(6);
        
        // Get revenue by subject
        $revenueBySubject = $this->getRevenueBySubject();
        
        // Get revenue by level
        $revenueByLevel = $this->getRevenueByLevel();
        
        return view('reports.index', compact(
            'totalRevenue',
            'currentMonthRevenue',
            'previousMonthRevenue',
            'revenueByMonth',
            'revenueBySubject',
            'revenueByLevel'
        ));
    }

    /**
     * Get revenue by month for the last X months.
     *
     * @param int $months
     * @return array
     */
    private function getRevenueByMonth($months = 6)
    {
        $result = [];
        
        for ($i = 0; $i < $months; $i++) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('F Y');
            $monthKey = $date->format('Y-m');
            
            $revenue = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                ->sum('paid_amount');
                
            $result[] = [
                'month' => $monthName,
                'revenue' => $revenue
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
        $students = Student::select('matiere')->whereNotNull('matiere')->get();
        
        if ($students->count() > 0) {
            $subjects = $students->pluck('matiere')
                ->map(function($matiere) {
                    return explode(', ', $matiere);
                })
                ->flatten()
                ->unique()
                ->values()
                ->toArray();
        }
        
        $result = [];
        
        foreach ($subjects as $subject) {
            $revenue = Student::where('matiere', 'like', "%{$subject}%")
                ->sum('paid_amount');
                
            $result[] = [
                'subject' => $subject,
                'revenue' => $revenue
            ];
        }
        
        // Sort by revenue in descending order
        usort($result, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });
        
        return $result;
    }

    /**
     * Get revenue by school level.
     *
     * @return array
     */
    private function getRevenueByLevel()
    {
        $levels = [
            'premiere_school' => 'Première School',
            '2_first_middle_niveau' => '2nd First Middle Niveau',
            '3ac' => '3AC',
            'high_school' => 'High School'
        ];
        
        $result = [];
        
        foreach ($levels as $key => $label) {
            $revenue = Student::where('niveau_scolaire', $key)
                ->sum('paid_amount');
                
            $count = Student::where('niveau_scolaire', $key)
                ->count();
                
            $result[] = [
                'level' => $label,
                'revenue' => $revenue,
                'count' => $count
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
            
            $revenue = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                ->sum('paid_amount');
                
            $count = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                ->count();
                
            $monthlyData[] = [
                'month' => $date->format('F'),
                'revenue' => $revenue,
                'count' => $count
            ];
        }
        
        // Get available years for the dropdown
        $years = Student::selectRaw('YEAR(created_at) as year')
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
     * @return \Illuminate\Http\Response
     */
    public function subjectRevenue()
    {
        $revenueBySubject = $this->getRevenueBySubject();
        
        return view('reports.subjects', compact('revenueBySubject'));
    }

    /**
     * Show level revenue report.
     *
     * @return \Illuminate\Http\Response
     */
    public function levelRevenue()
    {
        $revenueByLevel = $this->getRevenueByLevel();
        
        return view('reports.levels', compact('revenueByLevel'));
    }

    /**
     * Generate an export of student data.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportStudents(Request $request)
    {
        $query = Student::with(['course', 'communicationCourse']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('niveau_scolaire')) {
            $query->where('niveau_scolaire', $request->niveau_scolaire);
        }
        
        $students = $query->latest()->get();
        
        $filename = 'students_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Parent Name', 'Level', 
                'Course', 'Status', 'Paid Amount', 'Payment Expiry', 
                'Enrollment Date', 'Months'
            ]);
            
            // Add data rows
            foreach ($students as $student) {
                $levelLabels = [
                    'premiere_school' => 'Première School',
                    '2_first_middle_niveau' => '2nd First Middle Niveau',
                    '3ac' => '3AC',
                    'high_school' => 'High School'
                ];
                
                $level = $levelLabels[$student->niveau_scolaire] ?? $student->niveau_scolaire;
                
                fputcsv($file, [
                    $student->id,
                    $student->name,
                    $student->email,
                    $student->phone,
                    $student->parent_name,
                    $level,
                    $student->matiere,
                    $student->status,
                    $student->paid_amount,
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
        $query = Student::with(['course', 'communicationCourse']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('niveau_scolaire')) {
            $query->where('niveau_scolaire', $request->niveau_scolaire);
        }
        
        $students = $query->latest()->get();
        
        $levelLabels = [
            'premiere_school' => 'Première School',
            '2_first_middle_niveau' => '2nd First Middle Niveau',
            '3ac' => '3AC',
            'high_school' => 'High School'
        ];
        
        $pdf = PDF::loadView('reports.export-pdf', [
            'students' => $students,
            'levelLabels' => $levelLabels,
            'date' => Carbon::now()->format('Y-m-d')
        ]);
        
        return $pdf->download('students_export_' . date('Y-m-d') . '.pdf');
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
        $levelData = [];
        
        // Get levels
        $levels = [
            'premiere_school' => 'Première School',
            '2_first_middle_niveau' => '2nd First Middle Niveau',
            '3ac' => '3AC',
            'high_school' => 'High School'
        ];
        
        // Monthly data
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($year, $month, 1);
            $monthKey = $date->format('Y-m');
            $monthName = $date->format('F');
            
            $revenue = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                ->sum('paid_amount');
                
            $count = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                ->count();
                
            $monthlyData[] = [
                'month' => $monthName,
                'revenue' => $revenue,
                'count' => $count
            ];
            
            // Revenue by level for each month
            foreach ($levels as $key => $label) {
                $levelRevenue = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                    ->where('niveau_scolaire', $key)
                    ->sum('paid_amount');
                    
                $levelCount = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                    ->where('niveau_scolaire', $key)
                    ->count();
                    
                if (!isset($levelData[$key])) {
                    $levelData[$key] = [
                        'label' => $label,
                        'months' => []
                    ];
                }
                
                $levelData[$key]['months'][$monthName] = [
                    'revenue' => $levelRevenue,
                    'count' => $levelCount
                ];
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
        $levelData = [];
        
        // Get levels
        $levels = [
            'premiere_school' => 'Première School',
            '2_first_middle_niveau' => '2nd First Middle Niveau',
            '3ac' => '3AC',
            'high_school' => 'High School'
        ];
        
        // Monthly data
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($year, $month, 1);
            $monthKey = $date->format('Y-m');
            $monthName = $date->format('F');
            
            $revenue = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                ->sum('paid_amount');
                
            $count = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                ->count();
                
            $monthlyData[] = [
                'month' => $monthName,
                'revenue' => $revenue,
                'count' => $count
            ];
            
            // Revenue by level for each month
            foreach ($levels as $key => $label) {
                $levelRevenue = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                    ->where('niveau_scolaire', $key)
                    ->sum('paid_amount');
                    
                $levelCount = Student::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
                    ->where('niveau_scolaire', $key)
                    ->count();
                    
                if (!isset($levelData[$key])) {
                    $levelData[$key] = [
                        'label' => $label,
                        'months' => []
                    ];
                }
                
                $levelData[$key]['months'][$monthName] = [
                    'revenue' => $levelRevenue,
                    'count' => $levelCount
                ];
            }
        }
        
        // Get available years for the dropdown
        $years = Student::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [Carbon::now()->year];
        }
        
        return view('reports.month-level', compact('monthlyData', 'levelData', 'levels', 'years', 'year'));
    }
} 