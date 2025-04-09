@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Gestion des Inscriptions
            @elseif(session('locale') == 'ar')
                إدارة التسجيلات
            @else
                Enrollment Management
            @endif
        </h1>
        <div class="flex space-x-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                @if(session('locale') == 'fr')
                    Tableau de Bord
                @elseif(session('locale') == 'ar')
                    لوحة التحكم
                @else
                    Dashboard
                @endif
            </a>
            
            <a href="{{ route('student-courses.near-expiry') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @if(session('locale') == 'fr')
                    Expirations Proches
                @elseif(session('locale') == 'ar')
                    انتهاء قريب
                @else
                    Near Expiry
                @endif
            </a>
            
            <a href="{{ route('student-courses.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                @if(session('locale') == 'fr')
                    Nouvelle Inscription
                @elseif(session('locale') == 'ar')
                    تسجيل جديد
                @else
                    New Enrollment
                @endif
            </a>
        </div>
    </div>
    
    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Recherche et Filtres
                @elseif(session('locale') == 'ar')
                    البحث والتصفية
                @else
                    Search and Filters
                @endif
            </h2>
        </div>
        
        <form action="{{ route('student-courses.index') }}" method="GET" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Rechercher un Étudiant
                        @elseif(session('locale') == 'ar')
                            البحث عن طالب
                        @else
                            Search Student
                        @endif
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ session('locale') == 'fr' ? 'Nom ou Email...' : (session('locale') == 'ar' ? 'الاسم أو البريد الإلكتروني...' : 'Name or Email...') }}">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Statut
                        @elseif(session('locale') == 'ar')
                            الحالة
                        @else
                            Status
                        @endif
                    </label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">
                            @if(session('locale') == 'fr')
                                Tous les Statuts
                            @elseif(session('locale') == 'ar')
                                جميع الحالات
                            @else
                                All Statuses
                            @endif
                        </option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                            @if(session('locale') == 'fr')
                                Actif
                            @elseif(session('locale') == 'ar')
                                نشط
                            @else
                                Active
                            @endif
                        </option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            @if(session('locale') == 'fr')
                                Inactif
                            @elseif(session('locale') == 'ar')
                                غير نشط
                            @else
                                Inactive
                            @endif
                        </option>
                    </select>
                </div>
                
                <div>
                    <label for="course_type" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Type de Cours
                        @elseif(session('locale') == 'ar')
                            نوع الدورة
                        @else
                            Course Type
                        @endif
                    </label>
                    <select name="course_type" id="course_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">
                            @if(session('locale') == 'fr')
                                Tous les Types
                            @elseif(session('locale') == 'ar')
                                جميع الأنواع
                            @else
                                All Types
                            @endif
                        </option>
                        <option value="regular" {{ request('course_type') == 'regular' ? 'selected' : '' }}>
                            @if(session('locale') == 'fr')
                                Régulier
                            @elseif(session('locale') == 'ar')
                                منتظم
                            @else
                                Regular
                            @endif
                        </option>
                        <option value="communication" {{ request('course_type') == 'communication' ? 'selected' : '' }}>
                            @if(session('locale') == 'fr')
                                Communication
                            @elseif(session('locale') == 'ar')
                                التواصل
                            @else
                                Communication
                            @endif
                        </option>
                    </select>
                </div>
            </div>
            
            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if(session('locale') == 'fr')
                        Rechercher
                    @elseif(session('locale') == 'ar')
                        بحث
                    @else
                        Search
                    @endif
                </button>
            </div>
        </form>
    </div>
    
    <!-- Enrollments Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Liste des Inscriptions
                @elseif(session('locale') == 'ar')
                    قائمة التسجيلات
                @else
                    Enrollments List
                @endif
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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
                                Type
                            @elseif(session('locale') == 'ar')
                                النوع
                            @else
                                Type
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Date d'Expiration
                            @elseif(session('locale') == 'ar')
                                تاريخ الانتهاء
                            @else
                                Expiry Date
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
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Montant Payé
                            @elseif(session('locale') == 'ar')
                                المبلغ المدفوع
                            @else
                                Amount Paid
                            @endif
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Actions
                            @elseif(session('locale') == 'ar')
                                الإجراءات
                            @else
                                Actions
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $enrollment->student->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $enrollment->student->email ?? $enrollment->student->phone ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $enrollment->getCourseName() }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $enrollment->course->niveau_scolaire ?? $enrollment->communicationCourse->niveau_scolaire ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($enrollment->getCourseType() === 'regular')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    @if(session('locale') == 'fr')
                                        Régulier
                                    @elseif(session('locale') == 'ar')
                                        منتظم
                                    @else
                                        Regular
                                    @endif
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    @if(session('locale') == 'fr')
                                        Communication
                                    @elseif(session('locale') == 'ar')
                                        التواصل
                                    @else
                                        Communication
                                    @endif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm {{ $enrollment->isPaymentExpired() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                {{ $enrollment->payment_expiry ? $enrollment->payment_expiry->format('d/m/Y') : 'N/A' }}
                            </div>
                            @if(!$enrollment->isPaymentExpired() && $enrollment->payment_expiry)
                                <div class="text-xs text-gray-500">
                                    {{ $enrollment->getRemainingDays() }} 
                                    @if(session('locale') == 'fr')
                                        jours restants
                                    @elseif(session('locale') == 'ar')
                                        أيام متبقية
                                    @else
                                        days remaining
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($enrollment->status === 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    @if(session('locale') == 'fr')
                                        Actif
                                    @elseif(session('locale') == 'ar')
                                        نشط
                                    @else
                                        Active
                                    @endif
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    @if(session('locale') == 'fr')
                                        Inactif
                                    @elseif(session('locale') == 'ar')
                                        غير نشط
                                    @else
                                        Inactive
                                    @endif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            {{ number_format($enrollment->paid_amount, 2) }} DH
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="{{ route('student-courses.edit', $enrollment->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('student-courses.destroy', $enrollment->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ session('locale') == 'fr' ? 'Êtes-vous sûr de vouloir supprimer cette inscription?' : (session('locale') == 'ar' ? 'هل أنت متأكد من حذف هذا التسجيل؟' : 'Are you sure you want to delete this enrollment?') }}');">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            @if(session('locale') == 'fr')
                                Aucune inscription trouvée
                            @elseif(session('locale') == 'ar')
                                لم يتم العثور على تسجيلات
                            @else
                                No enrollments found
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            @if(isset($enrollments))
                {{ $enrollments->links() }}
            @endif
        </div>
    </div>
</div>
@endsection 