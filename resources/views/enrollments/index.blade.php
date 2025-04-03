@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Inscriptions
            @elseif(session('locale') == 'ar')
                التسجيلات
            @else
                Enrollments
            @endif
        </h1>
        <div class="flex space-x-3">
            <a href="{{ route('enrollments.revenue.by-subject') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                @if(session('locale') == 'fr')
                    Revenus par Matière
                @elseif(session('locale') == 'ar')
                    الإيرادات حسب المادة
                @else
                    Revenue by Subject
                @endif
            </a>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                @if(session('locale') == 'fr')
                    Retour au Tableau de Bord
                @elseif(session('locale') == 'ar')
                    العودة إلى لوحة التحكم
                @else
                    Back to Dashboard
                @endif
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                ID
                            @elseif(session('locale') == 'ar')
                                رقم
                            @else
                                ID
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Nombre d'Étudiants
                            @elseif(session('locale') == 'ar')
                                عدد الطلاب
                            @else
                                Student Count
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Niveau Scolaire
                            @elseif(session('locale') == 'ar')
                                المستوى الدراسي
                            @else
                                School Level
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
                                Statut
                            @elseif(session('locale') == 'ar')
                                الحالة
                            @else
                                Status
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Statut de Paiement
                            @elseif(session('locale') == 'ar')
                                حالة الدفع
                            @else
                                Payment Status
                            @endif
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Actions
                            @elseif(session('locale') == 'ar')
                                إجراءات
                            @else
                                Actions
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($enrollments as $enrollment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $enrollment->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $enrollment->student_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if(session('locale') == 'fr')
                                @if($enrollment->niveau_scolaire == 'premiere_school')
                                    Première École
                                @elseif($enrollment->niveau_scolaire == '2_first_middle_niveau')
                                    2ème Niveau Collège
                                @elseif($enrollment->niveau_scolaire == '3ac')
                                    3ème Année Collège
                                @elseif($enrollment->niveau_scolaire == 'high_school')
                                    Lycée
                                @else
                                    {{ $enrollment->niveau_scolaire }}
                                @endif
                            @elseif(session('locale') == 'ar')
                                @if($enrollment->niveau_scolaire == 'premiere_school')
                                    المدرسة الابتدائية
                                @elseif($enrollment->niveau_scolaire == '2_first_middle_niveau')
                                    المستوى الثاني متوسط
                                @elseif($enrollment->niveau_scolaire == '3ac')
                                    السنة الثالثة متوسط
                                @elseif($enrollment->niveau_scolaire == 'high_school')
                                    الثانوية
                                @else
                                    {{ $enrollment->niveau_scolaire }}
                                @endif
                            @else
                                {{ $enrollment->niveau_scolaire }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($enrollment->course)
                                {{ $enrollment->course->matiere }}
                            @elseif($enrollment->communicationCourse)
                                {{ $enrollment->communicationCourse->matiere }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                @if(session('locale') == 'fr')
                                    {{ $enrollment->status === 'active' ? 'Actif' : 'Inactif' }}
                                @elseif(session('locale') == 'ar')
                                    {{ $enrollment->status === 'active' ? 'نشط' : 'غير نشط' }}
                                @else
                                    {{ ucfirst($enrollment->status) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $enrollment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                @if(session('locale') == 'fr')
                                    {{ $enrollment->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                                @elseif(session('locale') == 'ar')
                                    {{ $enrollment->payment_status === 'paid' ? 'مدفوع' : 'قيد الانتظار' }}
                                @else
                                    {{ ucfirst($enrollment->payment_status) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <a href="#" class="text-blue-600 hover:text-blue-900 mx-1">
                                @if(session('locale') == 'fr')
                                    Voir
                                @elseif(session('locale') == 'ar')
                                    عرض
                                @else
                                    View
                                @endif
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 