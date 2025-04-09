@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Nouvelle Inscription
            @elseif(session('locale') == 'ar')
                تسجيل جديد
            @else
                New Enrollment
            @endif
        </h1>
        <a href="{{ route('student-courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            @if(session('locale') == 'fr')
                Retour
            @elseif(session('locale') == 'ar')
                رجوع
            @else
                Back
            @endif
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Détails de l'Inscription
                @elseif(session('locale') == 'ar')
                    تفاصيل التسجيل
                @else
                    Enrollment Details
                @endif
            </h2>
        </div>
        
        <form action="{{ route('student-courses.store') }}" method="POST" class="p-6">
            @csrf
            
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 text-red-500">
                    <p class="font-bold">
                        @if(session('locale') == 'fr')
                            Veuillez corriger les erreurs suivantes:
                        @elseif(session('locale') == 'ar')
                            يرجى تصحيح الأخطاء التالية:
                        @else
                            Please fix the following errors:
                        @endif
                    </p>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Étudiant
                        @elseif(session('locale') == 'ar')
                            الطالب
                        @else
                            Student
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="student_id" id="student_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="" selected disabled>
                            @if(session('locale') == 'fr')
                                Sélectionner un étudiant
                            @elseif(session('locale') == 'ar')
                                اختر طالب
                            @else
                                Select a student
                            @endif
                        </option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->email ?? $student->phone ?? 'N/A' }})
                            </option>
                        @endforeach
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
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="course_type" id="course_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="" selected disabled>
                            @if(session('locale') == 'fr')
                                Sélectionner un type
                            @elseif(session('locale') == 'ar')
                                اختر نوع
                            @else
                                Select a type
                            @endif
                        </option>
                        <option value="regular" {{ old('course_type') == 'regular' ? 'selected' : '' }}>
                            @if(session('locale') == 'fr')
                                Régulier
                            @elseif(session('locale') == 'ar')
                                منتظم
                            @else
                                Regular
                            @endif
                        </option>
                        <option value="communication" {{ old('course_type') == 'communication' ? 'selected' : '' }}>
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
                
                <div id="regular_course_container" class="hidden">
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Cours Régulier
                        @elseif(session('locale') == 'ar')
                            الدورة المنتظمة
                        @else
                            Regular Course
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="course_id" id="course_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="" selected disabled>
                            @if(session('locale') == 'fr')
                                Sélectionner un cours
                            @elseif(session('locale') == 'ar')
                                اختر دورة
                            @else
                                Select a course
                            @endif
                        </option>
                        @foreach($regularCourses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->matiere }} ({{ $course->niveau_scolaire }}) - {{ number_format($course->prix, 2) }} DH
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div id="communication_course_container" class="hidden">
                    <label for="communication_course_id" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Cours de Communication
                        @elseif(session('locale') == 'ar')
                            دورة التواصل
                        @else
                            Communication Course
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="communication_course_id" id="communication_course_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="" selected disabled>
                            @if(session('locale') == 'fr')
                                Sélectionner un cours
                            @elseif(session('locale') == 'ar')
                                اختر دورة
                            @else
                                Select a course
                            @endif
                        </option>
                        @foreach($communicationCourses as $course)
                            <option value="{{ $course->id }}" {{ old('communication_course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->matiere }} ({{ $course->niveau_scolaire }}) - {{ number_format($course->prix, 2) }} DH
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="enrollment_date" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Date d'Inscription
                        @elseif(session('locale') == 'ar')
                            تاريخ التسجيل
                        @else
                            Enrollment Date
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="enrollment_date" id="enrollment_date" value="{{ old('enrollment_date', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label for="paid_amount" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Montant Payé (DH)
                        @elseif(session('locale') == 'ar')
                            المبلغ المدفوع (درهم)
                        @else
                            Paid Amount (DH)
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="paid_amount" id="paid_amount" value="{{ old('paid_amount', 0) }}" step="0.01" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label for="months" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Nombre de Mois
                        @elseif(session('locale') == 'ar')
                            عدد الأشهر
                        @else
                            Number of Months
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="months" id="months" value="{{ old('months', 1) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label for="monthly_revenue_amount" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Revenu Mensuel (DH)
                        @elseif(session('locale') == 'ar')
                            الدخل الشهري (درهم)
                        @else
                            Monthly Revenue (DH)
                        @endif
                    </label>
                    <input type="number" name="monthly_revenue_amount" id="monthly_revenue_amount" value="{{ old('monthly_revenue_amount') }}" step="0.01" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">
                        @if(session('locale') == 'fr')
                            Laissez vide pour un calcul automatique basé sur le montant total et les mois
                        @elseif(session('locale') == 'ar')
                            اترك فارغا للحساب التلقائي بناءً على المبلغ الإجمالي والأشهر
                        @else
                            Leave empty for automatic calculation based on total amount and months
                        @endif
                    </p>
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
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                            @if(session('locale') == 'fr')
                                Actif
                            @elseif(session('locale') == 'ar')
                                نشط
                            @else
                                Active
                            @endif
                        </option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
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
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    @if(session('locale') == 'fr')
                        Créer l'Inscription
                    @elseif(session('locale') == 'ar')
                        إنشاء التسجيل
                    @else
                        Create Enrollment
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const courseTypeSelect = document.getElementById('course_type');
        const regularCourseContainer = document.getElementById('regular_course_container');
        const communicationCourseContainer = document.getElementById('communication_course_container');
        const courseIdSelect = document.getElementById('course_id');
        const communicationCourseIdSelect = document.getElementById('communication_course_id');
        
        function updateCourseContainers() {
            const selectedType = courseTypeSelect.value;
            
            if (selectedType === 'regular') {
                regularCourseContainer.classList.remove('hidden');
                communicationCourseContainer.classList.add('hidden');
                courseIdSelect.setAttribute('required', 'required');
                communicationCourseIdSelect.removeAttribute('required');
            } else if (selectedType === 'communication') {
                regularCourseContainer.classList.add('hidden');
                communicationCourseContainer.classList.remove('hidden');
                courseIdSelect.removeAttribute('required');
                communicationCourseIdSelect.setAttribute('required', 'required');
            } else {
                regularCourseContainer.classList.add('hidden');
                communicationCourseContainer.classList.add('hidden');
                courseIdSelect.removeAttribute('required');
                communicationCourseIdSelect.removeAttribute('required');
            }
        }
        
        // Initial setup
        updateCourseContainers();
        
        // Listen for changes
        courseTypeSelect.addEventListener('change', updateCourseContainers);
        
        // Calculate monthly revenue automatically
        const paidAmountInput = document.getElementById('paid_amount');
        const monthsInput = document.getElementById('months');
        const monthlyRevenueInput = document.getElementById('monthly_revenue_amount');
        
        function calculateMonthlyRevenue() {
            const paidAmount = parseFloat(paidAmountInput.value) || 0;
            const months = parseInt(monthsInput.value) || 1;
            
            if (paidAmount > 0 && months > 0) {
                const monthlyRevenue = (paidAmount / months).toFixed(2);
                monthlyRevenueInput.placeholder = monthlyRevenue;
            } else {
                monthlyRevenueInput.placeholder = '0.00';
            }
        }
        
        paidAmountInput.addEventListener('input', calculateMonthlyRevenue);
        monthsInput.addEventListener('input', calculateMonthlyRevenue);
        
        // Initial calculation
        calculateMonthlyRevenue();
    });
</script>
@endsection 