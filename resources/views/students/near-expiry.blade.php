@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Paiements expirant bientôt
            @elseif(session('locale') == 'ar')
                الدفعات التي ستنتهي قريباً
            @else
                Soon Expiring Payments
            @endif
        </h1>
        <div>
            <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors mr-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                @if(session('locale') == 'fr')
                    Retour
                @elseif(session('locale') == 'ar')
                    العودة
                @else
                    Back
                @endif
            </a>
        </div>
    </div>

    <!-- Filtering options -->
    <div class="mb-4 bg-white rounded-lg shadow-md p-4">
        <form action="{{ route('students.near-expiry') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">
                    @if(session('locale') == 'fr')
                        Niveau Scolaire
                    @elseif(session('locale') == 'ar')
                        المستوى الدراسي
                    @else
                        School Level
                    @endif
                </label>
                <select id="level" name="level" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">
                        @if(session('locale') == 'fr')
                            Tous les niveaux
                        @elseif(session('locale') == 'ar')
                            جميع المستويات
                        @else
                            All Levels
                        @endif
                    </option>
                    <option value="premiere_school" {{ request('level') == 'premiere_school' ? 'selected' : '' }}>
                        @if(session('locale') == 'fr')
                            Première École
                        @elseif(session('locale') == 'ar')
                            المدرسة الابتدائية
                        @else
                            Primary School
                        @endif
                    </option>
                    <option value="2_first_middle_niveau" {{ request('level') == '2_first_middle_niveau' ? 'selected' : '' }}>
                        @if(session('locale') == 'fr')
                            2ème Niveau Collège
                        @elseif(session('locale') == 'ar')
                            المستوى الثاني متوسط
                        @else
                            2nd Middle School
                        @endif
                    </option>
                    <option value="3ac" {{ request('level') == '3ac' ? 'selected' : '' }}>
                        @if(session('locale') == 'fr')
                            3ème Année Collège
                        @elseif(session('locale') == 'ar')
                            السنة الثالثة متوسط
                        @else
                            3rd Middle School
                        @endif
                    </option>
                    <option value="high_school" {{ request('level') == 'high_school' ? 'selected' : '' }}>
                        @if(session('locale') == 'fr')
                            Lycée
                        @elseif(session('locale') == 'ar')
                            الثانوية
                        @else
                            High School
                        @endif
                    </option>
                </select>
            </div>
            <div class="mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    @if(session('locale') == 'fr')
                        Filtrer
                    @elseif(session('locale') == 'ar')
                        تصفية
                    @else
                        Filter
                    @endif
                </button>
                <a href="{{ route('students.near-expiry') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
            <h2 class="text-lg font-semibold text-yellow-800">
                @if(session('locale') == 'fr')
                    Étudiants dont les paiements expirent dans les 5 prochains jours
                @elseif(session('locale') == 'ar')
                    الطلاب الذين ستنتهي مدفوعاتهم في الأيام الخمسة القادمة
                @else
                    Students with payments expiring in the next 5 days
                @endif
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                ID
                            @elseif(session('locale') == 'ar')
                                المعرف
                            @else
                                ID
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Nom
                            @elseif(session('locale') == 'ar')
                                الاسم
                            @else
                                Name
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Contact
                            @elseif(session('locale') == 'ar')
                                معلومات الاتصال
                            @else
                                Contact
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Matières
                            @elseif(session('locale') == 'ar')
                                المواد
                            @else
                                Subjects
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Date d'expiration
                            @elseif(session('locale') == 'ar')
                                تاريخ انتهاء الصلاحية
                            @else
                                Expiry Date
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Jours restants
                            @elseif(session('locale') == 'ar')
                                الأيام المتبقية
                            @else
                                Days Left
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
                    @forelse($students as $student)
                    <tr class="hover:bg-yellow-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                            @if($student->parent_name)
                            <div class="text-xs text-gray-500">
                                @if(session('locale') == 'fr')
                                    Parent: {{ $student->parent_name }}
                                @elseif(session('locale') == 'ar')
                                    ولي الأمر: {{ $student->parent_name }}
                                @else
                                    Parent: {{ $student->parent_name }}
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $student->phone }}</div>
                            @if($student->email)
                            <div class="text-xs text-gray-500">{{ $student->email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $student->matiere }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                {{ $student->payment_expiry->format('d M, Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $student->getRemainingDays() }} 
                                @if(session('locale') == 'fr')
                                    jours
                                @elseif(session('locale') == 'ar')
                                    أيام
                                @else
                                    days
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="{{ route('students.show', $student->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{{ route('students.edit', $student->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            @if(session('locale') == 'fr')
                                Aucun étudiant avec paiement expirant bientôt.
                            @elseif(session('locale') == 'ar')
                                لا يوجد طلاب بمدفوعات تنتهي قريباً.
                            @else
                                No students with soon expiring payments.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection 