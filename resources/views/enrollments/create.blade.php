@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Créer une Nouvelle Inscription
            @elseif(session('locale') == 'ar')
                إنشاء تسجيل جديد
            @else
                Create New Enrollment
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Informations d'Inscription
                @elseif(session('locale') == 'ar')
                    معلومات التسجيل
                @else
                    Enrollment Information
                @endif
            </h2>
        </div>
        
        <form action="{{ route('enrollments.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Student Information -->
                <div class="space-y-6">
                    <h3 class="text-base font-medium text-gray-900 border-b pb-2">
                        @if(session('locale') == 'fr')
                            Informations sur l'Étudiant
                        @elseif(session('locale') == 'ar')
                            معلومات الطالب
                        @else
                            Student Information
                        @endif
                    </h3>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            @if(session('locale') == 'fr')
                                Nom de l'Étudiant
                            @elseif(session('locale') == 'ar')
                                اسم الطالب
                            @else
                                Student Name
                            @endif
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            @if(session('locale') == 'fr')
                                Numéro de Téléphone
                            @elseif(session('locale') == 'ar')
                                رقم الهاتف
                            @else
                                Phone Number
                            @endif
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            @if(session('locale') == 'fr')
                                Email (Optionnel)
                            @elseif(session('locale') == 'ar')
                                البريد الإلكتروني (اختياري)
                            @else
                                Email (Optional)
                            @endif
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="parent_name" class="block text-sm font-medium text-gray-700 mb-1">
                            @if(session('locale') == 'fr')
                                Nom du Parent (Optionnel)
                            @elseif(session('locale') == 'ar')
                                اسم ولي الأمر (اختياري)
                            @else
                                Parent Name (Optional)
                            @endif
                        </label>
                        <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                            @if(session('locale') == 'fr')
                                Adresse (Optionnel)
                            @elseif(session('locale') == 'ar')
                                العنوان (اختياري)
                            @else
                                Address (Optional)
                            @endif
                        </label>
                        <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                    </div>
                </div>
                
                <!-- Enrollment Details -->
                <div class="space-y-6">
                    <h3 class="text-base font-medium text-gray-900 border-b pb-2">
                        @if(session('locale') == 'fr')
                            Détails de l'Inscription
                        @elseif(session('locale') == 'ar')
                            تفاصيل التسجيل
                        @else
                            Enrollment Details
                        @endif
                    </h3>
                    
                    <div>
                        <label for="niveau_scolaire" class="block text-sm font-medium text-gray-700 mb-1">
                            @if(session('locale') == 'fr')
                                Niveau Scolaire
                            @elseif(session('locale') == 'ar')
                                المستوى الدراسي
                            @else
                                School Level
                            @endif
                        </label>
                        <select name="niveau_scolaire" id="niveau_scolaire" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="" disabled selected>
                                @if(session('locale') == 'fr')
                                    Sélectionnez un niveau
                                @elseif(session('locale') == 'ar')
                                    اختر مستوى
                                @else
                                    Select a level
                                @endif
                            </option>
                            @foreach($schoolLevels as $value => $name)
                                <option value="{{ $value }}" {{ old('niveau_scolaire', $selectedLevel) == $value ? 'selected' : '' }}>
                                    @if(session('locale') == 'fr')
                                        @if($value == 'premiere_school')
                                            Première École
                                        @elseif($value == '2_first_middle_niveau')
                                            2ème Niveau Collège
                                        @elseif($value == '3ac')
                                            3ème Année Collège
                                        @elseif($value == 'high_school')
                                            Lycée
                                        @else
                                            {{ $name }}
                                        @endif
                                    @elseif(session('locale') == 'ar')
                                        @if($value == 'premiere_school')
                                            المدرسة الابتدائية
                                        @elseif($value == '2_first_middle_niveau')
                                            المستوى الثاني متوسط
                                        @elseif($value == '3ac')
                                            السنة الثالثة متوسط
                                        @elseif($value == 'high_school')
                                            الثانوية
                                        @else
                                            {{ $name }}
                                        @endif
                                    @else
                                        {{ $name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="student_count" class="block text-sm font-medium text-gray-700 mb-1">
                            @if(session('locale') == 'fr')
                                Nombre d'Étudiants
                            @elseif(session('locale') == 'ar')
                                عدد الطلاب
                            @else
                                Number of Students
                            @endif
                        </label>
                        <input type="number" name="student_count" id="student_count" value="{{ old('student_count', 1) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
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
                        </label>
                        <input type="number" name="months" id="months" value="{{ old('months', 1) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            @if(session('locale') == 'fr')
                                Sélection de Cours
                            @elseif(session('locale') == 'ar')
                                اختيار الدورات
                            @else
                                Course Selection
                            @endif
                        </label>
                        
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200 mb-4">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">
                                @if(session('locale') == 'fr')
                                    Cours Réguliers
                                @elseif(session('locale') == 'ar')
                                    الدورات العادية
                                @else
                                    Regular Courses
                                @endif
                            </h4>
                            <div class="space-y-3 ml-2">
                                @forelse($regularCourses as $level => $courses)
                                    @if($level == $selectedLevel || !$selectedLevel)
                                        @foreach($courses as $course)
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input type="checkbox" name="course_selections[]" id="course_{{ $course->id }}" value="regular:{{ $course->id }}" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" onchange="updateTotalPrice()">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="course_{{ $course->id }}" class="font-medium text-gray-700">{{ $course->matiere }}</label>
                                                    <p class="text-gray-500">{{ number_format($course->prix, 2) }} DH</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @empty
                                    <p class="text-sm text-gray-500">
                                        @if(session('locale') == 'fr')
                                            Aucun cours régulier disponible pour le niveau sélectionné.
                                        @elseif(session('locale') == 'ar')
                                            لا توجد دورات عادية متاحة للمستوى المحدد.
                                        @else
                                            No regular courses available for the selected level.
                                        @endif
                                    </p>
                                @endforelse
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">
                                @if(session('locale') == 'fr')
                                    Cours de Communication
                                @elseif(session('locale') == 'ar')
                                    دورات التواصل
                                @else
                                    Communication Courses
                                @endif
                            </h4>
                            <div class="space-y-3 ml-2">
                                {{-- Show all communication courses regardless of level --}}
                                @forelse($allCommunicationCourses as $course)
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="course_selections[]" id="comm_course_{{ $course->id }}" value="communication:{{ $course->id }}" class="focus:ring-blue-500 h-4 w-4 text-purple-600 border-gray-300 rounded" onchange="updateTotalPrice()">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="comm_course_{{ $course->id }}" class="font-medium text-gray-700">
                                                {{ $course->matiere }}
                                                <span class="text-xs text-gray-500">
                                                    @if(session('locale') == 'fr')
                                                        (Tous niveaux)
                                                    @elseif(session('locale') == 'ar')
                                                        (جميع المستويات)
                                                    @else
                                                        (All levels)
                                                    @endif
                                                </span>
                                            </label>
                                            <p class="text-gray-500">{{ number_format($course->prix, 2) }} DH</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">
                                        @if(session('locale') == 'fr')
                                            Aucun cours de communication disponible.
                                        @elseif(session('locale') == 'ar')
                                            لا توجد دورات تواصل متاحة.
                                        @else
                                            No communication courses available.
                                        @endif
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Price Section -->
            <div class="mt-6 bg-blue-50 p-4 rounded-md border border-blue-200">
                <h3 class="text-base font-medium text-blue-800 mb-2">
                    @if(session('locale') == 'fr')
                        Résumé des Frais
                    @elseif(session('locale') == 'ar')
                        ملخص الرسوم
                    @else
                        Fee Summary
                    @endif
                </h3>
                <div id="price_summary" class="text-sm"></div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
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
        const levelSelect = document.getElementById('niveau_scolaire');
        const monthsInput = document.getElementById('months');
        const studentCountInput = document.getElementById('student_count');
        
        // Initialize the total price calculation
        updateTotalPrice();
        
        levelSelect.addEventListener('change', function() {
            // Redirect to the same page with the selected level as a query parameter
            const selectedLevel = this.value;
            window.location.href = "{{ route('enrollments.create') }}?level=" + selectedLevel;
        });
        
        monthsInput.addEventListener('change', updateTotalPrice);
        studentCountInput.addEventListener('change', updateTotalPrice);
        
        // Make the function available globally
        window.updateTotalPrice = updateTotalPrice;
    });
    
    function updateTotalPrice() {
        const regularCourseCheckboxes = document.querySelectorAll('input[name="course_selections[]"][value^="regular:"]:checked');
        const commCourseCheckboxes = document.querySelectorAll('input[name="course_selections[]"][value^="communication:"]:checked');
        const months = parseInt(document.getElementById('months').value) || 1;
        const studentCount = parseInt(document.getElementById('student_count').value) || 1;
        const summaryDiv = document.getElementById('price_summary');
        
        // All courses data
        const regularCourses = @json($regularCourses);
        const communicationCourses = @json($communicationCourses);
        
        let totalBasePrice = 0;
        let html = '';
        
        // Add selected regular courses to the summary
        if (regularCourseCheckboxes.length > 0) {
            html += '<div class="mb-3">';
            html += '<div class="font-medium text-gray-700 mb-1">';
            
            if ('{{ session('locale') }}' === 'fr') {
                html += 'Cours Réguliers:';
            } else if ('{{ session('locale') }}' === 'ar') {
                html += 'الدورات العادية:';
            } else {
                html += 'Regular Courses:';
            }
            
            html += '</div>';
            html += '<ul class="list-disc pl-5 space-y-1">';
            
            regularCourseCheckboxes.forEach(checkbox => {
                const courseId = parseInt(checkbox.value.split(':')[1]);
                let course = null;
                
                // Find the course in regularCourses (which is grouped by level)
                Object.values(regularCourses).forEach(levelCourses => {
                    levelCourses.forEach(c => {
                        if (c.id === courseId) course = c;
                    });
                });
                
                if (course) {
                    const coursePrice = parseFloat(course.prix) * studentCount;
                    totalBasePrice += coursePrice;
                    
                    html += `<li class="flex justify-between">
                        <span>${course.matiere}</span>
                        <span>${coursePrice.toFixed(2)} DH</span>
                    </li>`;
                }
            });
            
            html += '</ul></div>';
        }
        
        // Add selected communication courses to the summary
        if (commCourseCheckboxes.length > 0) {
            html += '<div class="mb-3">';
            html += '<div class="font-medium text-gray-700 mb-1">';
            
            if ('{{ session('locale') }}' === 'fr') {
                html += 'Cours de Communication:';
            } else if ('{{ session('locale') }}' === 'ar') {
                html += 'دورات التواصل:';
            } else {
                html += 'Communication Courses:';
            }
            
            html += '</div>';
            html += '<ul class="list-disc pl-5 space-y-1">';
            
            commCourseCheckboxes.forEach(checkbox => {
                const courseId = parseInt(checkbox.value.split(':')[1]);
                let course = null;
                
                // Find the course in communicationCourses (which is grouped by level)
                Object.values(communicationCourses).forEach(levelCourses => {
                    levelCourses.forEach(c => {
                        if (c.id === courseId) course = c;
                    });
                });
                
                if (course) {
                    const coursePrice = parseFloat(course.prix) * studentCount;
                    totalBasePrice += coursePrice;
                    
                    html += `<li class="flex justify-between">
                        <span>${course.matiere}</span>
                        <span>${coursePrice.toFixed(2)} DH</span>
                    </li>`;
                }
            });
            
            html += '</ul></div>';
        }
        
        // Calculate total price based on months
        const totalPrice = totalBasePrice * months;
        
        // Add totals
        html += '<div class="mt-4 pt-3 border-t border-blue-200">';
        html += '<div class="flex justify-between font-medium">';
        
        if ('{{ session('locale') }}' === 'fr') {
            html += `<span>Prix mensuel de base (${studentCount} étudiants):</span>`;
        } else if ('{{ session('locale') }}' === 'ar') {
            html += `<span>السعر الشهري الأساسي (${studentCount} طلاب):</span>`;
        } else {
            html += `<span>Base monthly price (${studentCount} students):</span>`;
        }
        
        html += `<span>${totalBasePrice.toFixed(2)} DH</span>`;
        html += '</div>';
        
        html += '<div class="flex justify-between text-blue-800 font-bold mt-1">';
        
        if ('{{ session('locale') }}' === 'fr') {
            html += `<span>Total pour ${months} mois:</span>`;
        } else if ('{{ session('locale') }}' === 'ar') {
            html += `<span>إجمالي لمدة ${months} شهر:</span>`;
        } else {
            html += `<span>Total for ${months} months:</span>`;
        }
        
        html += `<span>${totalPrice.toFixed(2)} DH</span>`;
        html += '</div>';
        html += '</div>';
        
        // Update the summary div
        summaryDiv.innerHTML = html;
    }
</script>
@endsection 