<?php

use App\Http\Controllers\CoursPricingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommunicationCourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Language routes
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('language');

// Root redirect to login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

// Protected routes requiring authentication
Route::middleware(['auth'])->group(function () {
    // Student management
    Route::resource('students', StudentController::class);
    Route::get('students/near-expiry', [StudentController::class, 'nearExpiry'])->name('students.near-expiry');
    Route::get('students/restore/{id}', [StudentController::class, 'restore'])->name('students.restore');
    Route::get('students/{student}/receipt', [StudentController::class, 'generateReceipt'])->name('students.receipt');
    Route::get('/monthly-summary', [StudentController::class, 'monthlySummary'])->name('students.monthly-summary');
    
    // Student Courses management
    Route::get('student-courses/near-expiry', [EnrollmentController::class, 'nearExpiry'])->name('student-courses.near-expiry');
    Route::get('student-courses', [EnrollmentController::class, 'studentCoursesIndex'])->name('student-courses.index');
    Route::get('student-courses/create', [EnrollmentController::class, 'create'])->name('student-courses.create');
    Route::post('student-courses', [EnrollmentController::class, 'storeStudentCourse'])->name('student-courses.store');
    Route::get('student-courses/{id}/edit', [EnrollmentController::class, 'edit'])->name('student-courses.edit');
    Route::put('student-courses/{id}', [EnrollmentController::class, 'update'])->name('student-courses.update');
    Route::delete('student-courses/{id}', [EnrollmentController::class, 'destroy'])->name('student-courses.destroy');
    
    // Enrollment management
    Route::resource('enrollments', EnrollmentController::class);
    Route::get('/enrollments/revenue/by-subject', [EnrollmentController::class, 'revenueBySubject'])->name('enrollments.revenue.by-subject');
    Route::get('/enrollments/summary/{niveau_scolaire}', [EnrollmentController::class, 'summary'])->name('enrollments.summary');
    Route::get('/enrollments/level/{level}', [EnrollmentController::class, 'summaryByLevel'])->name('enrollments.level');

    // Course management
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [CoursPricingController::class, 'index'])->name('index');
        Route::get('/manage', [CoursPricingController::class, 'manage'])->name('manage');
        Route::post('/enroll', [CoursPricingController::class, 'enroll'])->name('enroll');
        Route::get('/enrollment/summary', [CoursPricingController::class, 'enrollmentSummary'])->name('enrollment.summary');
        Route::get('/enrollments/{id}', [CoursPricingController::class, 'courseEnrollments'])->name('enrollments');
        Route::post('/store', [CoursPricingController::class, 'store'])->name('store');
        Route::put('/update/{id}', [CoursPricingController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [CoursPricingController::class, 'destroy'])->name('destroy');
        Route::get('/restore/{id}', [CoursPricingController::class, 'restore'])->name('restore');
        Route::get('/level/{niveau_scolaire}', [CoursPricingController::class, 'showByLevel'])->name('level');
        Route::get('/{id}', [CoursPricingController::class, 'findCourse'])->name('details');
        Route::get('/edit/{id}', [CoursPricingController::class, 'edit'])->name('edit');
        Route::get('/create', [CoursPricingController::class, 'create'])->name('create');
        Route::get('/delete/{id}', [CoursPricingController::class, 'delete'])->name('delete');
    });

    // Communication courses management
    Route::prefix('communication-courses')->name('communication-courses.')->group(function () {
        Route::get('/', [CommunicationCourseController::class, 'index'])->name('index');
        Route::get('/manage', [CommunicationCourseController::class, 'manage'])->name('manage');
        Route::post('/enroll', [CommunicationCourseController::class, 'enroll'])->name('enroll');
        Route::get('/enrollment/summary', [CommunicationCourseController::class, 'enrollmentSummary'])->name('enrollment.summary');
        Route::get('/enrollments/{id}', [CommunicationCourseController::class, 'courseEnrollments'])->name('enrollments');
        Route::post('/store', [CommunicationCourseController::class, 'store'])->name('store');
        Route::put('/update/{id}', [CommunicationCourseController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [CommunicationCourseController::class, 'destroy'])->name('destroy');
        Route::get('/level/{niveau_scolaire}', [CommunicationCourseController::class, 'showByLevel'])->name('level');
        Route::get('/{id}', [CommunicationCourseController::class, 'findCourse'])->name('details');
        Route::get('/edit/{id}', [CommunicationCourseController::class, 'edit'])->name('edit');
        Route::get('/create', [CommunicationCourseController::class, 'create'])->name('create');
        Route::get('/delete/{id}', [CommunicationCourseController::class, 'delete'])->name('delete');
    });

    // Report routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/monthly', [ReportController::class, 'monthlyRevenue'])->name('monthly');
        Route::get('/subjects', [ReportController::class, 'subjects'])->name('subjects');
        Route::get('/subjects/revenue', [ReportController::class, 'subjectRevenue'])->name('subjects.revenue');
        Route::post('/subjects/revenue/export', [ReportController::class, 'exportCsv'])->name('subjects.revenue.export');
        Route::get('/levels', [ReportController::class, 'levelRevenue'])->name('levels');
        Route::get('/students/export', [ReportController::class, 'exportStudents'])->name('export.students');
        Route::get('/month-level', [ReportController::class, 'revenueByMonthAndLevel'])->name('month-level');
        Route::get('/export/month-level-pdf', [ReportController::class, 'exportRevenueByMonthAndLevel'])->name('export.month-level.pdf');
        Route::get('/monthly-breakdown', [ReportController::class, 'monthlyRevenueBreakdown'])->name('monthly-breakdown');
        Route::get('/export-subject-revenue', [ReportController::class, 'exportSubjectRevenue'])->name('export-subject-revenue');
    });
    
    // Export routes
    Route::post('/export-pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
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

// Authentication routes
Auth::routes();

// Home route
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Redirect home to dashboard
Route::redirect('/home', '/dashboard');
