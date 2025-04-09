@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Revenus par Matière et Niveau
            @elseif(session('locale') == 'ar')
                الإيرادات حسب المادة والمستوى
            @else
                Revenue by Subject and Level
            @endif
        </h1>
        <a href="{{ route('enrollments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            @if(session('locale') == 'fr')
                Retour aux Inscriptions
            @elseif(session('locale') == 'ar')
                العودة إلى التسجيلات
            @else
                Back to Enrollments
            @endif
        </a>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Filtres
                @elseif(session('locale') == 'ar')
                    تصفية
                @else
                    Filters
                @endif
            </h2>
        </div>
        <div class="p-6">
            <form action="{{ route('enrollments.revenue.by-subject') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-full md:w-auto">
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Niveau Scolaire
                        @elseif(session('locale') == 'ar')
                            المستوى الدراسي
                        @else
                            School Level
                        @endif
                    </label>
                    <select name="level" id="level" class="mt-1 block w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">
                            @if(session('locale') == 'fr')
                                Tous les niveaux
                            @elseif(session('locale') == 'ar')
                                جميع المستويات
                            @else
                                All levels
                            @endif
                        </option>
                        @foreach($schoolLevels as $levelKey => $levelName)
                            <option value="{{ $levelKey }}" {{ request('level') == $levelKey ? 'selected' : '' }}>
                                @if(session('locale') == 'fr')
                                    @if($levelKey == 'premiere_school')
                                        Première École
                                    @elseif($levelKey == '1ac')
                                        1ère Année Collège
                                    @elseif($levelKey == '2ac')
                                        2ème Année Collège
                                    @elseif($levelKey == 'high_school')
                                        Lycée
                                    @else
                                        {{ $levelName }}
                                    @endif
                                @elseif(session('locale') == 'ar')
                                    @if($levelKey == 'premiere_school')
                                        المدرسة الابتدائية
                                    @elseif($levelKey == '1ac')
                                        السنة الأولى متوسط
                                    @elseif($levelKey == '2ac')
                                        السنة الثانية متوسط
                                    @elseif($levelKey == 'high_school')
                                        الثانوية
                                    @else
                                        {{ $levelName }}
                                    @endif
                                @else
                                    {{ $levelName }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full md:w-auto">
                    <label for="course_type" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Type de Cours
                        @elseif(session('locale') == 'ar')
                            نوع الدورة
                        @else
                            Course Type
                        @endif
                    </label>
                    <select name="course_type" id="course_type" class="mt-1 block w-full md:w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">
                            @if(session('locale') == 'fr')
                                Tous les types
                            @elseif(session('locale') == 'ar')
                                جميع الأنواع
                            @else
                                All types
                            @endif
                        </option>
                        <option value="regular" {{ request('course_type') == 'regular' ? 'selected' : '' }}>
                            @if(session('locale') == 'fr')
                                Régulier
                            @elseif(session('locale') == 'ar')
                                عادي
                            @else
                                Regular
                            @endif
                        </option>
                        <option value="communication" {{ request('course_type') == 'communication' ? 'selected' : '' }}>
                            @if(session('locale') == 'fr')
                                Communication
                            @elseif(session('locale') == 'ar')
                                تواصل
                            @else
                                Communication
                            @endif
                        </option>
                    </select>
                </div>
                
                <div class="w-full md:w-auto">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Rechercher par Matière
                        @elseif(session('locale') == 'ar')
                            البحث حسب المادة
                        @else
                            Search by Subject
                        @endif
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ session('locale') == 'fr' ? 'Nom de la matière...' : (session('locale') == 'ar' ? 'اسم المادة...' : 'Subject name...') }}">
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        @if(session('locale') == 'fr')
                            Filtrer
                        @elseif(session('locale') == 'ar')
                            تصفية
                        @else
                            Filter
                        @endif
                    </button>
                    
                    <a href="{{ route('enrollments.revenue.by-subject') }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        @if(session('locale') == 'fr')
                            Réinitialiser
                        @elseif(session('locale') == 'ar')
                            إعادة تعيين
                        @else
                            Reset
                        @endif
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Résumé des Revenus
                @elseif(session('locale') == 'ar')
                    ملخص الإيرادات
                @else
                    Revenue Summary
                @endif
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @foreach($schoolLevels as $levelKey => $levelName)
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <h3 class="text-lg font-medium text-blue-800 mb-2">
                            @if(session('locale') == 'fr')
                                @if($levelKey == 'premiere_school')
                                    Première École
                                @elseif($levelKey == '1ac')
                                    1ère Année Collège
                                @elseif($levelKey == '2ac')
                                    2ème Année Collège
                                @elseif($levelKey == 'high_school')
                                    Lycée
                                @else
                                    {{ $levelName }}
                                @endif
                            @elseif(session('locale') == 'ar')
                                @if($levelKey == 'premiere_school')
                                    المدرسة الابتدائية
                                @elseif($levelKey == '1ac')
                                    السنة الأولى متوسط
                                @elseif($levelKey == '2ac')
                                    السنة الثانية متوسط
                                @elseif($levelKey == 'high_school')
                                    الثانوية
                                @else
                                    {{ $levelName }}
                                @endif
                            @else
                                {{ $levelName }}
                            @endif
                        </h3>
                        <div class="flex justify-between items-center text-sm text-blue-600 border-b border-blue-100 pb-2 mb-2">
                            <span>
                                @if(session('locale') == 'fr')
                                    Étudiants:
                                @elseif(session('locale') == 'ar')
                                    الطلاب:
                                @else
                                    Students:
                                @endif
                            </span>
                            <span class="font-semibold">{{ $totalsByLevel[$levelKey]['students'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-blue-700 font-medium">
                                @if(session('locale') == 'fr')
                                    Revenus:
                                @elseif(session('locale') == 'ar')
                                    الإيرادات:
                                @else
                                    Revenue:
                                @endif
                            </span>
                            <span class="text-blue-800 font-bold">{{ number_format($totalsByLevel[$levelKey]['revenue'], 2) }} DH</span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="bg-yellow-50 rounded-lg p-5 border border-yellow-100 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-xl font-semibold text-yellow-800">
                            @if(session('locale') == 'fr')
                                Total des Revenus
                            @elseif(session('locale') == 'ar')
                                إجمالي الإيرادات
                            @else
                                Total Revenue
                            @endif
                        </h3>
                        <p class="text-sm text-yellow-600 mt-1">
                            @if(session('locale') == 'fr')
                                Tous niveaux et matières combinés
                            @elseif(session('locale') == 'ar')
                                جميع المستويات والمواد مجتمعة
                            @else
                                All levels and subjects combined
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center">
                        <div class="mr-6 text-center">
                            <div class="text-sm text-yellow-600">
                                @if(session('locale') == 'fr')
                                    Étudiants Totaux
                                @elseif(session('locale') == 'ar')
                                    إجمالي الطلاب
                                @else
                                    Total Students
                                @endif
                            </div>
                            <div class="text-lg font-semibold text-yellow-700">{{ $actualStudentCount  ?? $totalStudents }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-yellow-600">
                                @if(session('locale') == 'fr')
                                    Revenus Totaux
                                @elseif(session('locale') == 'ar')
                                    إجمالي الإيرادات
                                @else
                                    Total Revenue
                                @endif
                            </div>
                            <div class="text-2xl font-bold text-yellow-800">{{ number_format($grandTotal, 2) }} DH</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Répartition Détaillée des Revenus
                @elseif(session('locale') == 'ar')
                    التفاصيل الكاملة للإيرادات
                @else
                    Detailed Revenue Breakdown
                @endif
            </h2>
            <span class="text-sm text-gray-600">
                @if(session('locale') == 'fr')
                    {{ $filteredCount ?? $revenueBySubject->count() }} résultats
                @elseif(session('locale') == 'ar')
                    {{ $filteredCount ?? $revenueBySubject->count() }} نتيجة
                @else
                    {{ $filteredCount ?? $revenueBySubject->count() }} results
                @endif
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Matière
                            @elseif(session('locale') == 'ar')
                                المادة
                            @else
                                Subject
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Niveau
                            @elseif(session('locale') == 'ar')
                                المستوى
                            @else
                                Level
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Type
                            @elseif(session('locale') == 'ar')
                                النوع
                            @else
                                Type
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Étudiants
                            @elseif(session('locale') == 'ar')
                                الطلاب
                            @else
                                Students
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Revenus
                            @elseif(session('locale') == 'ar')
                                الإيرادات
                            @else
                                Revenue
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($revenueBySubject as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->subject }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if(session('locale') == 'fr')
                                    @if($item->level == 'premiere_school')
                                        Première École
                                    @elseif($item->level == '1ac')
                                        1ère Année Collège
                                    @elseif($item->level == '2ac')
                                        2ème Année Collège
                                    @elseif($item->level == 'high_school')
                                        Lycée
                                    @else
                                        {{ $item->level }}
                                    @endif
                                @elseif(session('locale') == 'ar')
                                    @if($item->level == 'premiere_school')
                                        المدرسة الابتدائية
                                    @elseif($item->level == '1ac')
                                        السنة الأولى متوسط
                                    @elseif($item->level == '2ac')
                                        السنة الثانية متوسط
                                    @elseif($item->level == 'high_school')
                                        الثانوية
                                    @else
                                        {{ $item->level }}
                                    @endif
                                @else
                                    @if($item->level == 'premiere_school')
                                        Primary School
                                    @elseif($item->level == '1ac')
                                        1st Middle School
                                    @elseif($item->level == '2ac')
                                        2nd Middle School
                                    @elseif($item->level == 'high_school')
                                        High School
                                    @else
                                        {{ $item->level }}
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($item->course_type == 'regular')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        @if(session('locale') == 'fr')
                                            Régulier
                                        @elseif(session('locale') == 'ar')
                                            عادي
                                        @else
                                            Regular
                                        @endif
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        @if(session('locale') == 'fr')
                                            Communication
                                        @elseif(session('locale') == 'ar')
                                            تواصل
                                        @else
                                            Communication
                                        @endif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                {{ $item->student_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <span class="text-green-600 font-semibold">{{ number_format($item->total_revenue, 2) }} DH</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                @if(session('locale') == 'fr')
                                    Aucun revenu trouvé.
                                @elseif(session('locale') == 'ar')
                                    لم يتم العثور على إيرادات.
                                @else
                                    No revenue data found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 