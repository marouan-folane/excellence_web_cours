<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Student;
use App\Models\Cours;
use App\Models\CommunicationCourse;
use App\Models\Enrollment;
use App\Http\Controllers\API\StudentAPIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::prefix('v1')->group(function () {
    // Database status check
    Route::get('/status', function () {
        try {
            $tables = \DB::select('SHOW TABLES');
            return response()->json([
                'status' => 'connected',
                'database' => env('DB_DATABASE'),
                'tables_count' => count($tables)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Get courses list
    Route::get('/courses', function () {
        $courses = Cours::select('id', 'matiere', 'niveau_scolaire', 'prix', 'type')
            ->orderBy('niveau_scolaire')
            ->orderBy('matiere')
            ->get();
        
        return response()->json($courses);
    });

    // Get communication courses list
    Route::get('/communication-courses', function () {
        $courses = CommunicationCourse::select('id', 'matiere', 'niveau_scolaire', 'prix')
            ->orderBy('niveau_scolaire')
            ->orderBy('matiere')
            ->get();
        
        return response()->json($courses);
    });

    // Get dashboard stats
    Route::get('/stats', function () {
        $studentsCount = Student::count();
        $coursesCount = Cours::count() + CommunicationCourse::count();
        $enrollmentsCount = Enrollment::count();
        $totalRevenue = Student::sum('paid_amount');
        
        $stats = [
            'students_count' => $studentsCount,
            'courses_count' => $coursesCount,
            'enrollments_count' => $enrollmentsCount,
            'total_revenue' => $totalRevenue
        ];
        
        return response()->json($stats);
    });
});

// Protected API routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Student routes using the controller
    Route::get('/students', [StudentAPIController::class, 'index']);
    Route::post('/students', [StudentAPIController::class, 'store']);
    Route::get('/students/{id}', [StudentAPIController::class, 'show']);
    Route::put('/students/{id}', [StudentAPIController::class, 'update']);
    Route::delete('/students/{id}', [StudentAPIController::class, 'destroy']);
    Route::get('/students/near-expiry', [StudentAPIController::class, 'nearExpiry']);
    Route::get('/students/monthly-summary', [StudentAPIController::class, 'monthlyPriceSummary']);
    
    // Get expiring payments
    Route::get('/expiring-payments', function () {
        $fiveDaysLater = \Carbon\Carbon::now()->addDays(5);
        $today = \Carbon\Carbon::now();
        
        $students = Student::where('payment_expiry', '>', $today)
            ->where('payment_expiry', '<=', $fiveDaysLater)
            ->where('status', 'active')
            ->with(['course', 'communicationCourse'])
            ->get();
            
        return response()->json($students);
    });
}); 