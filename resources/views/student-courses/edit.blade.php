@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Modifier l'Inscription
            @elseif(session('locale') == 'ar')
                تعديل التسجيل
            @else
                Edit Enrollment
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
        
        <form action="{{ route('student-courses.update', $enrollment->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
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
                    <label for="student_info" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Étudiant
                        @elseif(session('locale') == 'ar')
                            الطالب
                        @else
                            Student
                        @endif
                    </label>
                    <div class="mt-1 p-2 rounded-md border border-gray-300 bg-gray-50">
                        <p class="font-medium">{{ $enrollment->student->name }}</p>
                        <p class="text-sm text-gray-600">{{ $enrollment->student->email ?? $enrollment->student->phone ?? 'N/A' }}</p>
                    </div>
                    <input type="hidden" name="student_id" value="{{ $enrollment->student_id }}">
                </div>
                
                <div>
                    <label for="course_info" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Cours
                        @elseif(session('locale') == 'ar')
                            الدورة
                        @else
                            Course
                        @endif
                    </label>
                    <div class="mt-1 p-2 rounded-md border border-gray-300 bg-gray-50">
                        <p class="font-medium">{{ $enrollment->getCourseName() }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $enrollment->course->niveau_scolaire ?? $enrollment->communicationCourse->niveau_scolaire ?? 'N/A' }} - 
                            {{ $enrollment->getCourseType() === 'regular' ? 
                                (session('locale') == 'fr' ? 'Régulier' : (session('locale') == 'ar' ? 'منتظم' : 'Regular')) : 
                                (session('locale') == 'fr' ? 'Communication' : (session('locale') == 'ar' ? 'التواصل' : 'Communication')) 
                            }}
                        </p>
                    </div>
                    <input type="hidden" name="course_type" value="{{ $enrollment->getCourseType() }}">
                    <input type="hidden" name="course_id" value="{{ $enrollment->course_id }}">
                    <input type="hidden" name="communication_course_id" value="{{ $enrollment->communication_course_id }}">
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
                    <input type="date" name="enrollment_date" id="enrollment_date" value="{{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('Y-m-d') : '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label for="payment_expiry" class="block text-sm font-medium text-gray-700 mb-1">
                        @if(session('locale') == 'fr')
                            Date d'Expiration du Paiement
                        @elseif(session('locale') == 'ar')
                            تاريخ انتهاء الدفع
                        @else
                            Payment Expiry Date
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="payment_expiry" id="payment_expiry" value="{{ $enrollment->payment_expiry ? $enrollment->payment_expiry->format('Y-m-d') : '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
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
                    <input type="number" name="paid_amount" id="paid_amount" value="{{ old('paid_amount', $enrollment->paid_amount) }}" step="0.01" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
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
                    <input type="number" name="months" id="months" value="{{ old('months', $enrollment->months) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
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
                    <input type="number" name="monthly_revenue_amount" id="monthly_revenue_amount" value="{{ old('monthly_revenue_amount', $enrollment->monthly_revenue_amount) }}" step="0.01" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                        <option value="active" {{ old('status', $enrollment->status) == 'active' ? 'selected' : '' }}>
                            @if(session('locale') == 'fr')
                                Actif
                            @elseif(session('locale') == 'ar')
                                نشط
                            @else
                                Active
                            @endif
                        </option>
                        <option value="inactive" {{ old('status', $enrollment->status) == 'inactive' ? 'selected' : '' }}>
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
            
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    @if(session('locale') == 'fr')
                        ID d'Inscription: {{ $enrollment->id }}
                    @elseif(session('locale') == 'ar')
                        معرف التسجيل: {{ $enrollment->id }}
                    @else
                        Enrollment ID: {{ $enrollment->id }}
                    @endif
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors" 
                            onclick="if(confirm('{{ session('locale') == 'fr' ? 'Êtes-vous sûr de vouloir supprimer cette inscription?' : (session('locale') == 'ar' ? 'هل أنت متأكد من حذف هذا التسجيل؟' : 'Are you sure you want to delete this enrollment?') }}')) document.getElementById('delete-form').submit();">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        @if(session('locale') == 'fr')
                            Supprimer
                        @elseif(session('locale') == 'ar')
                            حذف
                        @else
                            Delete
                        @endif
                    </button>
                    
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        @if(session('locale') == 'fr')
                            Enregistrer les Modifications
                        @elseif(session('locale') == 'ar')
                            حفظ التغييرات
                        @else
                            Save Changes
                        @endif
                    </button>
                </div>
            </div>
        </form>
        
        <form id="delete-form" action="{{ route('student-courses.destroy', $enrollment->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
    
    <!-- Payment History Section - Could be expanded later -->
    <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Informations sur le Cours
                @elseif(session('locale') == 'ar')
                    معلومات الدورة
                @else
                    Course Information
                @endif
            </h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($enrollment->course)
                    <div>
                        <h3 class="text-md font-semibold text-gray-700 mb-2">
                            @if(session('locale') == 'fr')
                                Détails du Cours Régulier
                            @elseif(session('locale') == 'ar')
                                تفاصيل الدورة المنتظمة
                            @else
                                Regular Course Details
                            @endif
                        </h3>
                        <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
                            <p class="mb-2">
                                <span class="font-medium">
                                    @if(session('locale') == 'fr')
                                        Nom du Cours:
                                    @elseif(session('locale') == 'ar')
                                        اسم الدورة:
                                    @else
                                        Course Name:
                                    @endif
                                </span> 
                                {{ $enrollment->course->matiere }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">
                                    @if(session('locale') == 'fr')
                                        Niveau Scolaire:
                                    @elseif(session('locale') == 'ar')
                                        المستوى الدراسي:
                                    @else
                                        School Level:
                                    @endif
                                </span> 
                                {{ $enrollment->course->niveau_scolaire }}
                            </p>
                            <p>
                                <span class="font-medium">
                                    @if(session('locale') == 'fr')
                                        Prix:
                                    @elseif(session('locale') == 'ar')
                                        السعر:
                                    @else
                                        Price:
                                    @endif
                                </span> 
                                {{ number_format($enrollment->course->prix, 2) }} DH
                            </p>
                        </div>
                    </div>
                @elseif($enrollment->communicationCourse)
                    <div>
                        <h3 class="text-md font-semibold text-gray-700 mb-2">
                            @if(session('locale') == 'fr')
                                Détails du Cours de Communication
                            @elseif(session('locale') == 'ar')
                                تفاصيل دورة التواصل
                            @else
                                Communication Course Details
                            @endif
                        </h3>
                        <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
                            <p class="mb-2">
                                <span class="font-medium">
                                    @if(session('locale') == 'fr')
                                        Nom du Cours:
                                    @elseif(session('locale') == 'ar')
                                        اسم الدورة:
                                    @else
                                        Course Name:
                                    @endif
                                </span> 
                                {{ $enrollment->communicationCourse->matiere }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">
                                    @if(session('locale') == 'fr')
                                        Niveau Scolaire:
                                    @elseif(session('locale') == 'ar')
                                        المستوى الدراسي:
                                    @else
                                        School Level:
                                    @endif
                                </span> 
                                {{ $enrollment->communicationCourse->niveau_scolaire }}
                            </p>
                            <p>
                                <span class="font-medium">
                                    @if(session('locale') == 'fr')
                                        Prix:
                                    @elseif(session('locale') == 'ar')
                                        السعر:
                                    @else
                                        Price:
                                    @endif
                                </span> 
                                {{ number_format($enrollment->communicationCourse->prix, 2) }} DH
                            </p>
                        </div>
                    </div>
                @endif
                
                <div>
                    <h3 class="text-md font-semibold text-gray-700 mb-2">
                        @if(session('locale') == 'fr')
                            Informations sur le Paiement
                        @elseif(session('locale') == 'ar')
                            معلومات الدفع
                        @else
                            Payment Information
                        @endif
                    </h3>
                    <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
                        <p class="mb-2">
                            <span class="font-medium">
                                @if(session('locale') == 'fr')
                                    Montant Total Payé:
                                @elseif(session('locale') == 'ar')
                                    إجمالي المبلغ المدفوع:
                                @else
                                    Total Paid Amount:
                                @endif
                            </span> 
                            {{ number_format($enrollment->paid_amount, 2) }} DH
                        </p>
                        <p class="mb-2">
                            <span class="font-medium">
                                @if(session('locale') == 'fr')
                                    Revenu Mensuel:
                                @elseif(session('locale') == 'ar')
                                    الدخل الشهري:
                                @else
                                    Monthly Revenue:
                                @endif
                            </span> 
                            {{ number_format($enrollment->monthly_revenue_amount ?: $enrollment->calculateMonthlyRevenue(), 2) }} DH
                        </p>
                        <p>
                            <span class="font-medium">
                                @if(session('locale') == 'fr')
                                    Statut du Paiement:
                                @elseif(session('locale') == 'ar')
                                    حالة الدفع:
                                @else
                                    Payment Status:
                                @endif
                            </span> 
                            @if($enrollment->isPaymentExpired())
                                <span class="text-red-600 font-medium">
                                    @if(session('locale') == 'fr')
                                        Expiré
                                    @elseif(session('locale') == 'ar')
                                        منتهي
                                    @else
                                        Expired
                                    @endif
                                </span>
                            @else
                                <span class="text-green-600 font-medium">
                                    @if(session('locale') == 'fr')
                                        Actif ({{ $enrollment->getRemainingDays() }} jours restants)
                                    @elseif(session('locale') == 'ar')
                                        نشط ({{ $enrollment->getRemainingDays() }} أيام متبقية)
                                    @else
                                        Active ({{ $enrollment->getRemainingDays() }} days remaining)
                                    @endif
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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