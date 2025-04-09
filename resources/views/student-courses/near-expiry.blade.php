@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Inscriptions Proches de l'Expiration
            @elseif(session('locale') == 'ar')
                التسجيلات القريبة من الانتهاء
            @else
                Near-Expiry Enrollments
            @endif
        </h1>
        <div class="flex space-x-3">
            <a href="{{ route('student-courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                @if(session('locale') == 'fr')
                    Toutes les Inscriptions
                @elseif(session('locale') == 'ar')
                    جميع التسجيلات
                @else
                    All Enrollments
                @endif
            </a>
        </div>
    </div>
    
    <!-- Near-Expiry Alert -->
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    @if(session('locale') == 'fr')
                        Les inscriptions suivantes expirent dans les 14 prochains jours. Veuillez contacter les étudiants pour renouveler.
                    @elseif(session('locale') == 'ar')
                        التسجيلات التالية ستنتهي خلال 14 يومًا القادمة. يرجى الاتصال بالطلاب للتجديد.
                    @else
                        The following enrollments are expiring in the next 14 days. Please contact the students for renewal.
                    @endif
                </p>
            </div>
        </div>
    </div>
    
    <!-- Enrollments Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Inscriptions Proches de l'Expiration
                @elseif(session('locale') == 'ar')
                    التسجيلات القريبة من الانتهاء
                @else
                    Near-Expiry Enrollments
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
                                Date d'Expiration
                            @elseif(session('locale') == 'ar')
                                تاريخ الانتهاء
                            @else
                                Expiry Date
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Jours Restants
                            @elseif(session('locale') == 'ar')
                                الأيام المتبقية
                            @else
                                Days Remaining
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
                    @forelse($nearExpiryEnrollments as $enrollment)
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
                                {{ $enrollment->getCourseType() === 'regular' ? 
                                    (session('locale') == 'fr' ? 'Régulier' : (session('locale') == 'ar' ? 'منتظم' : 'Regular')) : 
                                    (session('locale') == 'fr' ? 'Communication' : (session('locale') == 'ar' ? 'التواصل' : 'Communication')) 
                                }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-yellow-600">
                                {{ $enrollment->payment_expiry ? $enrollment->payment_expiry->format('d/m/Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold {{ $enrollment->getRemainingDays() <= 7 ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ $enrollment->getRemainingDays() }} 
                                @if(session('locale') == 'fr')
                                    jours
                                @elseif(session('locale') == 'ar')
                                    أيام
                                @else
                                    days
                                @endif
                            </div>
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            @if(session('locale') == 'fr')
                                Aucune inscription proche de l'expiration trouvée
                            @elseif(session('locale') == 'ar')
                                لم يتم العثور على تسجيلات قريبة من الانتهاء
                            @else
                                No near-expiry enrollments found
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            @if(isset($nearExpiryEnrollments))
                {{ $nearExpiryEnrollments->links() }}
            @endif
        </div>
    </div>
</div>
@endsection 