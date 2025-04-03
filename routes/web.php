<?php

use App\Http\Controllers\CoursPricingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommunicationCourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Database test route
Route::get('/db-test', function () {
    try {
        $tables = DB::select('SHOW TABLES');
        $dbInfo = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_INFO);
        $connectionInfo = [
            'Connected' => true,
            'Database' => env('DB_DATABASE'),
            'Tables' => count($tables),
            'Tables List' => array_map(function($table) {
                return reset($table);
            }, $tables)
        ];
        return response()->json($connectionInfo);
    } catch (\Exception $e) {
        return response()->json([
            'Connected' => false,
            'Error' => $e->getMessage()
        ], 500);
    }
});

// Language switching
Route::get('/language/{locale}', [LanguageController::class, 'setLocale'])->name('set.locale');

// Course pricing routes - public
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CoursPricingController::class, 'index'])->name('index');
    Route::get('/level/{niveau_scolaire}', [CoursPricingController::class, 'showByLevel'])->name('level');
});

// Communication courses routes - public
Route::prefix('communication-courses')->name('communication-courses.')->group(function () {
    Route::get('/', [CommunicationCourseController::class, 'index'])->name('index');
    Route::get('/level/{niveau_scolaire}', [CommunicationCourseController::class, 'showByLevel'])->name('level');
});

// Authentication routes
Auth::routes();

// Protected routes requiring authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Student management
    Route::get('/students/near-expiry', [StudentController::class, 'nearExpiry'])->name('students.near-expiry');
    Route::resource('students', StudentController::class);
    Route::get('/monthly-summary', [StudentController::class, 'monthlyPriceSummary'])->name('students.monthly-summary');
    
    // Enrollments management
    Route::get('/enrollments/create', [EnrollmentController::class, 'showEnrollmentForm'])->name('enrollments.create');
    Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/revenue/by-subject', [EnrollmentController::class, 'revenueBySubject'])->name('enrollments.revenue.by-subject');
    Route::get('/enrollments/summary/{niveau_scolaire}', [EnrollmentController::class, 'summary'])->name('enrollments.summary');
    Route::get('/enrollments/level/{level}', [EnrollmentController::class, 'summaryByLevel'])->name('enrollments.level');
    
    // Course management (protected)
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/manage', [CoursPricingController::class, 'manage'])->name('manage');
        Route::post('/enroll', [CoursPricingController::class, 'enroll'])->name('enroll');
        Route::get('/enrollment/summary', [CoursPricingController::class, 'enrollmentSummary'])->name('enrollment.summary');
        Route::post('/store', [CoursPricingController::class, 'store'])->name('store');
        Route::put('/update/{id}', [CoursPricingController::class, 'update'])->name('update');
    });

    // Communication courses management (protected)
    Route::prefix('communication-courses')->name('communication-courses.')->group(function () {
        Route::get('/manage', [CommunicationCourseController::class, 'manage'])->name('manage');
        Route::post('/enroll', [CommunicationCourseController::class, 'enroll'])->name('enroll');
        Route::get('/enrollment/summary', [CommunicationCourseController::class, 'enrollmentSummary'])->name('enrollment.summary');
    });

    // Report routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/monthly', [ReportController::class, 'monthlyRevenue'])->name('monthly');
        Route::get('/subjects', [ReportController::class, 'subjectRevenue'])->name('subjects');
        Route::get('/levels', [ReportController::class, 'levelRevenue'])->name('levels');
        Route::get('/month-level', [ReportController::class, 'revenueByMonthAndLevel'])->name('month-level');
        
        // Export routes
        Route::get('/export/students', [ReportController::class, 'exportStudents'])->name('export.students');
        Route::get('/export/students-pdf', [ReportController::class, 'exportPdf'])->name('export.students.pdf');
        Route::get('/export/month-level-pdf', [ReportController::class, 'exportRevenueByMonthAndLevel'])->name('export.month-level.pdf');
        Route::get('/export/csv', [ReportController::class, 'exportCsv'])->name('export.csv');
    });
});

// Redirect /home to /dashboard
Route::redirect('/home', '/dashboard');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
