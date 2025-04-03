@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">
            @if(session('locale') == 'fr')
                Tableau de bord
            @elseif(session('locale') == 'ar')
                لوحة التحكم
            @else
                Dashboard
            @endif
        </h1>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Students -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">
                            @if(session('locale') == 'fr')
                                Total Étudiants
                            @elseif(session('locale') == 'ar')
                                إجمالي الطلاب
                            @else
                                Total Students
                            @endif
                        </div>
                        <div class="text-xl font-semibold text-gray-800">{{ $studentsCount }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Total Courses -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">
                            @if(session('locale') == 'fr')
                                Total Cours
                            @elseif(session('locale') == 'ar')
                                إجمالي الدورات
                            @else
                                Total Courses
                            @endif
                        </div>
                        <div class="text-xl font-semibold text-gray-800">{{ $coursesCount }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Total Enrollments -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">
                            @if(session('locale') == 'fr')
                                Total Inscriptions
                            @elseif(session('locale') == 'ar')
                                إجمالي التسجيلات
                            @else
                                Total Enrollments
                            @endif
                        </div>
                        <div class="text-xl font-semibold text-gray-800">{{ $enrollmentsCount }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">
                            @if(session('locale') == 'fr')
                                Revenus Totaux
                            @elseif(session('locale') == 'ar')
                                إجمالي الإيرادات
                            @else
                                Total Revenue
                            @endif
                        </div>
                        <div class="text-xl font-semibold text-gray-800">{{ number_format($totalRevenue, 2) }} DH</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Links Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    @if(session('locale') == 'fr')
                        Liens Rapides
                    @elseif(session('locale') == 'ar')
                        روابط سريعة
                    @else
                        Quick Links
                    @endif
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Students Link -->
                <a href="{{ route('students.near-expiry') }}" class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 transition">
                    <span class="p-2 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <span class="font-medium text-gray-700">
                        @if(session('locale') == 'fr')
                            Paiements Bientôt Expirés
                        @elseif(session('locale') == 'ar')
                            المدفوعات التي ستنتهي قريباً
                        @else
                            Near-Expiry Payments
                        @endif
                    </span>
                </a>

                <!-- Create Enrollment Link -->
                <a href="{{ route('enrollments.create') }}" class="flex items-center p-4 bg-green-50 rounded-lg border border-green-100 hover:bg-green-100 transition">
                    <span class="p-2 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </span>
                    <span class="font-medium text-gray-700">
                        @if(session('locale') == 'fr')
                            Nouvelle Inscription
                        @elseif(session('locale') == 'ar')
                            تسجيل جديد
                        @else
                            New Enrollment
                        @endif
                    </span>
                </a>

                <!-- Revenue By Subject Link -->
                <a href="{{ route('enrollments.revenue.by-subject') }}" class="flex items-center p-4 bg-yellow-50 rounded-lg border border-yellow-100 hover:bg-yellow-100 transition">
                    <span class="p-2 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </span>
                    <span class="font-medium text-gray-700">
                        @if(session('locale') == 'fr')
                            Revenus par Matière
                        @elseif(session('locale') == 'ar')
                            الإيرادات حسب المادة
                        @else
                            Revenue By Subject
                        @endif
                    </span>
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <!-- Near Expiry Payments Section -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-yellow-700">
                            @if(session('locale') == 'fr')
                                Paiements expirant bientôt
                            @elseif(session('locale') == 'ar')
                                المدفوعات التي تنتهي قريباً
                            @else
                                Soon Expiring Payments
                            @endif
                        </h2>
                        <a href="{{ route('students.near-expiry') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            @if(session('locale') == 'fr')
                                Voir Tout
                            @elseif(session('locale') == 'ar')
                                عرض الكل
                            @else
                                View All
                            @endif
                        </a>
                    </div>
                    
                    @php
                        $fiveDaysLater = \Carbon\Carbon::now()->addDays(5);
                        $today = \Carbon\Carbon::now();
                        $nearExpiryCount = \App\Models\Student::where('status', 'active')
                            ->where('payment_expiry', '>', $today)
                            ->where('payment_expiry', '<=', $fiveDaysLater)
                            ->count();
                    @endphp
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    @if(session('locale') == 'fr')
                                        Vous avez <span class="font-bold">{{ $nearExpiryCount }}</span> étudiants dont le paiement expire dans les 5 prochains jours.
                                    @elseif(session('locale') == 'ar')
                                        لديك <span class="font-bold">{{ $nearExpiryCount }}</span> طلاب ستنتهي مدفوعاتهم في الأيام الخمسة القادمة.
                                    @else
                                        You have <span class="font-bold">{{ $nearExpiryCount }}</span> students with payments expiring in the next 5 days.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        @if(session('locale') == 'fr')
                            Inscriptions Récentes
                        @elseif(session('locale') == 'ar')
                            التسجيلات الأخيرة
                        @else
                            Recent Enrollments
                        @endif
                    </h2>
                    
                    @if(count($recentEnrollments) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @if(session('locale') == 'fr')
                                            Étudiant
                                        @elseif(session('locale') == 'ar')
                                            الطالب
                                        @else
                                            Student
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @if(session('locale') == 'fr')
                                            Cours
                                        @elseif(session('locale') == 'ar')
                                            الدورة
                                        @else
                                            Course
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @if(session('locale') == 'fr')
                                            Date
                                        @elseif(session('locale') == 'ar')
                                            التاريخ
                                        @else
                                            Date
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @if(session('locale') == 'fr')
                                            Statut
                                        @elseif(session('locale') == 'ar')
                                            الحالة
                                        @else
                                            Status
                                        @endif
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($recentEnrollments as $enrollment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $enrollment->student->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($enrollment->course)
                                            {{ $enrollment->course->matiere }} 
                                            @if(session('locale') == 'fr')
                                                (Régulier)
                                            @elseif(session('locale') == 'ar')
                                                (منتظم)
                                            @else
                                                (Regular)
                                            @endif
                                        @elseif($enrollment->communicationCourse)
                                            {{ $enrollment->communicationCourse->matiere }} 
                                            @if(session('locale') == 'fr')
                                                (Communication)
                                            @elseif(session('locale') == 'ar')
                                                (تواصل)
                                            @else
                                                (Communication)
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $enrollment->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            @if(session('locale') == 'fr')
                                                Actif
                                            @elseif(session('locale') == 'ar')
                                                نشط
                                            @else
                                                Active
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-gray-500">
                        @if(session('locale') == 'fr')
                            Aucune inscription récente trouvée.
                        @elseif(session('locale') == 'ar')
                            لم يتم العثور على تسجيلات حديثة.
                        @else
                            No recent enrollments found.
                        @endif
                    </p>
                    @endif
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        @if(session('locale') == 'fr')
                            Étudiants par Niveau
                        @elseif(session('locale') == 'ar')
                            الطلاب حسب المستوى
                        @else
                            Students by Level
                        @endif
                    </h2>
                    
                    @if(count($studentsByLevel) > 0)
                    <div class="space-y-4">
                        @foreach($studentsByLevel as $level)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">
                                @if($level->niveau_scolaire == 'premiere_school')
                                    @if(session('locale') == 'fr')
                                        Première École
                                    @elseif(session('locale') == 'ar')
                                        المدرسة الابتدائية
                                    @else
                                        Primary School
                                    @endif
                                @elseif($level->niveau_scolaire == '2_first_middle_niveau')
                                    @if(session('locale') == 'fr')
                                        2ème Collège
                                    @elseif(session('locale') == 'ar')
                                        السنة الثانية إعدادي
                                    @else
                                        2nd Middle School
                                    @endif
                                @elseif($level->niveau_scolaire == '3ac')
                                    @if(session('locale') == 'fr')
                                        3ème Collège
                                    @elseif(session('locale') == 'ar')
                                        السنة الثالثة إعدادي
                                    @else
                                        3rd Middle School
                                    @endif
                                @elseif($level->niveau_scolaire == 'high_school')
                                    @if(session('locale') == 'fr')
                                        Lycée
                                    @elseif(session('locale') == 'ar')
                                        الثانوية
                                    @else
                                        High School
                                    @endif
                                @else
                                    {{ $level->niveau_scolaire }}
                                @endif
                            </span>
                            <span class="text-sm font-semibold text-gray-800">{{ $level->student_count }} 
                                @if(session('locale') == 'fr')
                                    étudiants
                                @elseif(session('locale') == 'ar')
                                    طلاب
                                @else
                                    students
                                @endif
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($level->student_count / $studentsCount) * 100 }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-medium text-gray-600">
                                @if(session('locale') == 'fr')
                                    Revenus Mensuels:
                                @elseif(session('locale') == 'ar')
                                    الإيرادات الشهرية:
                                @else
                                    Monthly Revenue:
                                @endif
                            </span>
                            <span class="font-bold text-green-600">{{ number_format($level->level_total_price, 2) }} DH</span>
                        </div>
                        <hr class="my-2 border-gray-200">
                        @endforeach
                        
                        <div class="mt-4 pt-4 border-t-2 border-gray-300">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-700">
                                    @if(session('locale') == 'fr')
                                        Revenus Mensuels Totaux:
                                    @elseif(session('locale') == 'ar')
                                        إجمالي الإيرادات الشهرية:
                                    @else
                                        Total Monthly Revenue:
                                    @endif
                                </span>
                                <span class="text-base font-bold text-green-600">
                                    {{ number_format($studentsByLevel->sum('level_total_price'), 2) }} DH
                                </span>
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="text-gray-500">
                        @if(session('locale') == 'fr')
                            Aucune donnée d'étudiant disponible.
                        @elseif(session('locale') == 'ar')
                            لا تتوفر بيانات للطلاب.
                        @else
                            No student data available.
                        @endif
                    </p>
                    @endif
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        @if(session('locale') == 'fr')
                            Actions Rapides
                        @elseif(session('locale') == 'ar')
                            إجراءات سريعة
                        @else
                            Quick Actions
                        @endif
                    </h2>
                    
                    <div class="space-y-2">
                        <a href="{{ route('enrollments.create') }}" class="w-full block text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            @if(session('locale') == 'fr')
                                Nouvelle Inscription
                            @elseif(session('locale') == 'ar')
                                تسجيل جديد
                            @else
                                New Enrollment
                            @endif
                        </a>
                        <a href="{{ route('courses.manage') }}" class="w-full block text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                            @if(session('locale') == 'fr')
                                Gérer les Cours
                            @elseif(session('locale') == 'ar')
                                إدارة الدورات
                            @else
                                Manage Courses
                            @endif
                        </a>
                        <a href="{{ route('students.index') }}" class="w-full block text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                            @if(session('locale') == 'fr')
                                Voir les Étudiants
                            @elseif(session('locale') == 'ar')
                                عرض الطلاب
                            @else
                                View Students
                            @endif
                        </a>
                        <a href="{{ route('students.near-expiry') }}" class="w-full block text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                            @if(session('locale') == 'fr')
                                Paiements Expirant Bientôt
                            @elseif(session('locale') == 'ar')
                                المدفوعات التي تنتهي قريباً
                            @else
                                Soon Expiring Payments
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="p-4 bg-white rounded-lg shadow">
    <h4 class="text-base font-semibold text-gray-800 mb-3">
        @if(session('locale') == 'fr')
            Gestion des Étudiants
        @elseif(session('locale') == 'ar')
            إدارة الطلاب
        @else
            Student Management
        @endif
    </h4>
    <div class="flex flex-col gap-2">
        <a href="{{ route('students.index') }}" class="w-full block text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
            @if(session('locale') == 'fr')
                Tous les Étudiants
            @elseif(session('locale') == 'ar')
                جميع الطلاب
            @else
                All Students
            @endif
        </a>
        <a href="{{ route('students.create') }}" class="w-full block text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
            @if(session('locale') == 'fr')
                Ajouter un Étudiant
            @elseif(session('locale') == 'ar')
                إضافة طالب
            @else
                Add Student
            @endif
        </a>
        <a href="{{ route('enrollments.revenue.by-subject') }}" class="w-full block text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-cyan-600 hover:bg-cyan-700">
            @if(session('locale') == 'fr')
                Revenus par Matière
            @elseif(session('locale') == 'ar')
                الإيرادات حسب المادة
            @else
                Revenue by Subject
            @endif
        </a>
        <a href="{{ route('students.near-expiry') }}" class="w-full block text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
            @if(session('locale') == 'fr')
                Paiements Expirant Bientôt
            @elseif(session('locale') == 'ar')
                المدفوعات التي تنتهي قريباً
            @else
                Soon Expiring Payments
            @endif
        </a>
    </div>
</div>
@endsection 