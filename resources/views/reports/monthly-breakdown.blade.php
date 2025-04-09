@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Répartition Mensuelle des Revenus
            @elseif(session('locale') == 'ar')
                التقسيم الشهري للإيرادات
            @else
                Monthly Revenue Breakdown
            @endif
            - {{ $monthName }}
        </h2>
        
        <div class="flex space-x-2">
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                @if(session('locale') == 'fr')
                    Retour aux Rapports
                @elseif(session('locale') == 'ar')
                    العودة إلى التقارير
                @else
                    Back to Reports
                @endif
            </a>
        </div>
    </div>
    
    <!-- Month and Year Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('reports.monthly-breakdown') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">
                    @if(session('locale') == 'fr')
                        Année
                    @elseif(session('locale') == 'ar')
                        السنة
                    @else
                        Year
                    @endif
                </label>
                <select id="year" name="year" class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 mb-1">
                    @if(session('locale') == 'fr')
                        Mois
                    @elseif(session('locale') == 'ar')
                        الشهر
                    @else
                        Month
                    @endif
                </label>
                <select id="month" name="month" class="block w-36 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($months as $m => $monthName)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="self-end mb-1">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    @if(session('locale') == 'fr')
                        Filtrer
                    @elseif(session('locale') == 'ar')
                        تصفية
                    @else
                        Filter
                    @endif
                </button>
            </div>
        </form>
    </div>
    
    <!-- Summary Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Résumé de la Période
                @elseif(session('locale') == 'ar')
                    ملخص الفترة
                @else
                    Period Summary
                @endif
            </h3>
            
            <div class="text-2xl font-bold text-green-600">
                {{ number_format($totalMonthlyRevenue, 2) }} DH
            </div>
        </div>
        
        <div class="mt-4">
            <div class="text-sm text-gray-600">
                @if(session('locale') == 'fr')
                    Nombre total d'étudiants actifs ce mois-ci
                @elseif(session('locale') == 'ar')
                    إجمالي عدد الطلاب النشطين هذا الشهر
                @else
                    Total active students this month
                @endif
            </div>
            <div class="text-lg font-semibold">
                {{ count($studentEnrollments) }}
            </div>
        </div>
    </div>
    
    <!-- Students List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Revenus Détaillés par Étudiant
                @elseif(session('locale') == 'ar')
                    الإيرادات المفصلة حسب الطالب
                @else
                    Detailed Revenue by Student
                @endif
            </h3>
        </div>
        
        @if(count($studentEnrollments) > 0)
            <div class="divide-y divide-gray-200">
                @foreach($studentEnrollments as $studentId => $data)
                    <div class="px-6 py-4">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-2">
                            <div>
                                <h4 class="text-lg font-medium text-gray-800">{{ $data['student']->name }}</h4>
                                <div class="text-sm text-gray-600">
                                    @if($data['student']->niveau_scolaire == 'premiere_school')
                                        @if(session('locale') == 'fr')
                                            Première École
                                        @elseif(session('locale') == 'ar')
                                            المدرسة الابتدائية
                                        @else
                                            Primary School
                                        @endif
                                    @elseif($data['student']->niveau_scolaire == '1ac')
                                        @if(session('locale') == 'fr')
                                            1ère Année Collège
                                        @elseif(session('locale') == 'ar')
                                            السنة الأولى إعدادي
                                        @else
                                            1st Middle School
                                        @endif
                                    @elseif($data['student']->niveau_scolaire == '2ac')
                                        @if(session('locale') == 'fr')
                                            2ème Année Collège
                                        @elseif(session('locale') == 'ar')
                                            السنة الثانية إعدادي
                                        @else
                                            2nd Middle School
                                        @endif
                                    @elseif($data['student']->niveau_scolaire == '3ac')
                                        @if(session('locale') == 'fr')
                                            3ème Année Collège
                                        @elseif(session('locale') == 'ar')
                                            السنة الثالثة إعدادي
                                        @else
                                            3rd Middle School
                                        @endif
                                    @elseif($data['student']->niveau_scolaire == 'high_school')
                                        @if(session('locale') == 'fr')
                                            Lycée
                                        @elseif(session('locale') == 'ar')
                                            الثانوية
                                        @else
                                            High School
                                        @endif
                                    @else
                                        {{ $data['student']->niveau_scolaire }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-lg font-semibold text-gray-800 mt-2 lg:mt-0">
                                {{ number_format($data['total'], 2) }} DH
                            </div>
                        </div>
                        
                        <!-- Course enrollments for this student -->
                        <div class="mt-2">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                @if(session('locale') == 'fr')
                                                    Cours
                                                @elseif(session('locale') == 'ar')
                                                    المادة
                                                @else
                                                    Course
                                                @endif
                                            </th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                @if(session('locale') == 'fr')
                                                    Type
                                                @elseif(session('locale') == 'ar')
                                                    النوع
                                                @else
                                                    Type
                                                @endif
                                            </th>
                                            <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                @if(session('locale') == 'fr')
                                                    Revenu Mensuel
                                                @elseif(session('locale') == 'ar')
                                                    الإيراد الشهري
                                                @else
                                                    Monthly Revenue
                                                @endif
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($data['enrollments'] as $enrollmentData)
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                                                    {{ $enrollmentData['course_name'] }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600">
                                                    @if($enrollmentData['course_type'] == 'regular')
                                                        @if(session('locale') == 'fr')
                                                            Régulier
                                                        @elseif(session('locale') == 'ar')
                                                            عادي
                                                        @else
                                                            Regular
                                                        @endif
                                                    @elseif($enrollmentData['course_type'] == 'communication')
                                                        @if(session('locale') == 'fr')
                                                            Communication
                                                        @elseif(session('locale') == 'ar')
                                                            تواصل
                                                        @else
                                                            Communication
                                                        @endif
                                                    @else
                                                        {{ $enrollmentData['course_type'] }}
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right font-medium text-green-600">
                                                    {{ number_format($enrollmentData['monthly_revenue'], 2) }} DH
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                @if(session('locale') == 'fr')
                    Aucun revenu trouvé pour cette période
                @elseif(session('locale') == 'ar')
                    لم يتم العثور على إيرادات لهذه الفترة
                @else
                    No revenue found for this period
                @endif
            </div>
        @endif
    </div>
</div>
@endsection 