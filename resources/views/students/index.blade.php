@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Étudiants
            @elseif(session('locale') == 'ar')
                الطلاب
            @else
                Students
            @endif
        </h1>
        <div class="flex space-x-2">
            <a href="{{ route('student-courses.near-expiry') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                @if(session('locale') == 'fr')
                    Paiements expirant bientôt
                @elseif(session('locale') == 'ar')
                    المدفوعات التي تنتهي قريباً
                @else
                    Soon Expiring Payments
                @endif
            </a>
            <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                @if(session('locale') == 'fr')
                    Ajouter un étudiant
                @elseif(session('locale') == 'ar')
                    إضافة طالب
                @else
                    Add Student
                @endif
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('students.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                    @if(session('locale') == 'fr')
                        Recherche
                    @elseif(session('locale') == 'ar')
                        بحث
                    @else
                        Search
                    @endif
                </label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ session('locale') == 'fr' ? 'Nom ou téléphone...' : (session('locale') == 'ar' ? 'الاسم أو الهاتف...' : 'Name or phone...') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="niveau_scolaire" class="block text-sm font-medium text-gray-700 mb-1">
                    @if(session('locale') == 'fr')
                        Niveau scolaire
                    @elseif(session('locale') == 'ar')
                        المستوى الدراسي
                    @else
                        School Level
                    @endif
                </label>
                <select name="niveau_scolaire" id="niveau_scolaire" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">
                        @if(session('locale') == 'fr')
                            Tous les niveaux
                        @elseif(session('locale') == 'ar')
                            جميع المستويات
                        @else
                            All Levels
                        @endif
                    </option>
                    <option value="premiere_school" {{ request('niveau_scolaire') == 'premiere_school' ? 'selected' : '' }}>Première École</option>
                    <option value="1ac" {{ request('niveau_scolaire') == '1ac' ? 'selected' : '' }}>1ère Année Collège</option>
                    <option value="2ac" {{ request('niveau_scolaire') == '2ac' ? 'selected' : '' }}>2ème Année Collège</option>
                    <option value="3ac" {{ request('niveau_scolaire') == '3ac' ? 'selected' : '' }}>3éme Année Collège</option>
                    <option value="tronc_commun" {{ request('niveau_scolaire') == 'tronc_commun' ? 'selected' : '' }}>Tronc Commun</option>
                    <option value="deuxieme_annee" {{ request('niveau_scolaire') == 'deuxieme_annee' ? 'selected' : '' }}>2ème Année Lycée</option>
                    <option value="bac" {{ request('niveau_scolaire') == 'bac' ? 'selected' : '' }}>Baccalauréat</option>
                </select>
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
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">
                        @if(session('locale') == 'fr')
                            Tous les statuts
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
                <label for="payment" class="block text-sm font-medium text-gray-700 mb-1">
                    @if(session('locale') == 'fr')
                        Statut de paiement
                    @elseif(session('locale') == 'ar')
                        حالة الدفع
                    @else
                        Payment Status
                    @endif
                </label>
                <select name="payment" id="payment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">
                        @if(session('locale') == 'fr')
                            Tous les paiements
                        @elseif(session('locale') == 'ar')
                            جميع المدفوعات
                        @else
                            All Payments
                        @endif
                    </option>
                    <option value="valid" {{ request('payment') == 'valid' ? 'selected' : '' }}>
                        @if(session('locale') == 'fr')
                            Paiements valides
                        @elseif(session('locale') == 'ar')
                            مدفوعات سارية المفعول
                        @else
                            Valid Payments
                        @endif
                    </option>
                    <option value="expired" {{ request('payment') == 'expired' ? 'selected' : '' }}>
                        @if(session('locale') == 'fr')
                            Paiements expirés
                        @elseif(session('locale') == 'ar')
                            مدفوعات منتهية
                        @else
                            Expired Payments
                        @endif
                    </option>
                    <option value="near-expiry" {{ request('payment') == 'near-expiry' ? 'selected' : '' }}>
                        @if(session('locale') == 'fr')
                            Paiements expirant bientôt
                        @elseif(session('locale') == 'ar')
                            مدفوعات تنتهي قريباً
                        @else
                            Soon Expiring Payments
                        @endif
                    </option>
                </select>
            </div>
            <div class="flex items-end lg:col-span-4">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Liste des étudiants
                @elseif(session('locale') == 'ar')
                    قائمة الطلاب
                @else
                    Students List
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
                                Niveau
                            @elseif(session('locale') == 'ar')
                                المستوى
                            @else
                                Level
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if(session('locale') == 'fr')
                                Nombre d'étudiants
                            @elseif(session('locale') == 'ar')
                                عدد الطلاب
                            @else
                                Student Count
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
                    <tr>
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $student->niveau_scolaire }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->student_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                @if(session('locale') == 'fr')
                                    {{ $student->status === 'active' ? 'Actif' : 'Inactif' }}
                                @elseif(session('locale') == 'ar')
                                    {{ $student->status === 'active' ? 'نشط' : 'غير نشط' }}
                                @else
                                    {{ ucfirst($student->status) }}
                                @endif
                            </span>
                            
                            @if($student->payment_expiry)
                                @php
                                    $daysLeft = $student->getRemainingDays();
                                    $isFutureEnrollment = $student->enrollment_date && \Carbon\Carbon::parse($student->enrollment_date)->gt(\Carbon\Carbon::now());
                                    $colorClass = $daysLeft <= 0 ? 'bg-red-100 text-red-800' : 
                                                ($daysLeft <= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                    
                                    // For future enrollments, use a different color
                                    if ($isFutureEnrollment) {
                                        $colorClass = 'bg-purple-100 text-purple-800';
                                    }
                                @endphp
                                <div class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                        @if($isFutureEnrollment)
                                            @if(session('locale') == 'fr')
                                                Commence dans {{ now()->diffInDays($student->enrollment_date) }} jours
                                            @elseif(session('locale') == 'ar')
                                                يبدأ خلال {{ now()->diffInDays($student->enrollment_date) }} أيام
                                            @else
                                                Starts in {{ now()->diffInDays($student->enrollment_date) }} days
                                            @endif
                                        @elseif($daysLeft <= 0)
                                            @if(session('locale') == 'fr')
                                                Expiré
                                            @elseif(session('locale') == 'ar')
                                                منتهي
                                            @else
                                                Expired
                                            @endif
                                        @else
                                            @if(session('locale') == 'fr')
                                                {{ $daysLeft }} jours restants
                                            @elseif(session('locale') == 'ar')
                                                {{ $daysLeft }} أيام متبقية
                                            @else
                                                {{ $daysLeft }} days left
                                            @endif
                                        @endif
                                    </span>
                                </div>
                            @endif
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
                            <a href="{{ route('students.receipt', $student) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded border border-blue-500 bg-blue-100 text-blue-700 hover:bg-blue-200" title="Generate Receipt" target="_blank">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="sr-only">Receipt</span>
                            </a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ session('locale') == 'fr' ? 'Êtes-vous sûr de vouloir supprimer cet étudiant?' : (session('locale') == 'ar' ? 'هل أنت متأكد من رغبتك في حذف هذا الطالب؟' : 'Are you sure you want to delete this student?') }}')">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            @if(session('locale') == 'fr')
                                Aucun étudiant trouvé.
                            @elseif(session('locale') == 'ar')
                                لم يتم العثور على طلاب.
                            @else
                                No students found.
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