@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800">Edit Student: {{ $student->name ?? 'ID #' . $student->id }}</h1>
            <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('students.update', $student) }}" method="POST" id="studentForm">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h2>
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Student Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $student->phone) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="parent_name" class="block text-sm font-medium text-gray-700 mb-1">Parent Name</label>
                            <input type="text" id="parent_name" name="parent_name" value="{{ old('parent_name', $student->parent_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea id="address" name="address" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address', $student->address) }}</textarea>
                        </div>
                    </div>

                    <!-- Course Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Course Information</h2>
                        
                        <div class="mb-4">
                            <label for="niveau_scolaire" class="block text-sm font-medium text-gray-700 mb-1">School Level *</label>
                            <select id="niveau_scolaire" name="niveau_scolaire" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Level</option>
                                @foreach($niveau_scolaires as $value => $label)
                                    <option value="{{ $value }}" {{ old('niveau_scolaire', $student->niveau_scolaire) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Available Courses *</label>
                            <div id="courses_container" class="border border-gray-300 rounded-md p-4 bg-white max-h-56 overflow-y-auto">
                                <div class="text-gray-500 text-sm">Loading courses based on selected level...</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selected Courses Summary</label>
                            <div id="selected_courses" class="border border-gray-300 rounded-md p-4 bg-white min-h-[100px]">
                                <div class="text-gray-500 text-sm">No courses selected</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="months" class="block text-sm font-medium text-gray-700 mb-1">Number of Months *</label>
                                <input type="number" id="months" name="months" min="1"
                                       value="{{ old('months', $student->months ?? 1) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <p class="mt-1 text-xs text-gray-500">Payment expiry will be calculated based on this value.</p>
                            </div>
                            
                            <div>
                                <label for="paid_amount" class="block text-sm font-medium text-gray-700 mb-1">Paid Amount (DH) *</label>
                                <input type="number" id="paid_amount" name="paid_amount" 
                                       value="{{ old('paid_amount', $student->paid_amount) }}" step="0.01" min="0" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="enrollment_date" class="block text-sm font-medium text-gray-700 mb-1">Enrollment Date *</label>
                                <input type="date" id="enrollment_date" name="enrollment_date" 
                                       value="{{ old('enrollment_date', $student->enrollment_date ? $student->enrollment_date->format('Y-m-d') : date('Y-m-d')) }}" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            
                            <div>
                                <label for="payment_expiry" class="block text-sm font-medium text-gray-700 mb-1">Payment Expiry Date *</label>
                                <input type="date" id="payment_expiry" name="payment_expiry" 
                                       value="{{ old('payment_expiry', $student->payment_expiry ? $student->payment_expiry->format('Y-m-d') : date('Y-m-d', strtotime('+1 month'))) }}" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Hidden field for student_count, always value 1 -->
                        <input type="hidden" name="student_count" value="1">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"></path>
                        </svg>
                        Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const niveauSelect = document.getElementById('niveau_scolaire');
    const coursesContainer = document.getElementById('courses_container');
    const selectedCoursesDiv = document.getElementById('selected_courses');
    const paidAmountInput = document.getElementById('paid_amount');
    const monthsInput = document.getElementById('months');
    
    // Setup data
    const selectedRegularCourseIds = @json($selectedRegularCourseIds);
    const selectedCommCourseIds = @json($selectedCommCourseIds);
    let courses = @json($courses);
    let communicationCourses = @json($communicationCourses);
    
    function updateCourses() {
        const selectedLevel = niveauSelect.value;
        const levelCourses = courses.filter(course => course.niveau_scolaire === selectedLevel);
        
        // Filter communication courses to show only those appropriate for the selected level
        const levelCommCourses = communicationCourses.filter(course => 
            course.niveau_scolaire === selectedLevel || course.niveau_scolaire === 'all');
        
        // Check if we already have existing courses
        const hasExistingRegularCourses = selectedRegularCourseIds.length > 0;
        const hasExistingCommCourses = selectedCommCourseIds.length > 0;
        
        let html = '';
        
        // Show banner if student has existing courses
        if (hasExistingRegularCourses || hasExistingCommCourses) {
            html += `
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                This student already has courses assigned. 
                                <strong>Existing courses will be preserved unless unchecked.</strong>
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Regular Courses Section
        if (levelCourses.length > 0) {
            html += '<div class="mb-4"><div class="font-medium text-gray-800 mb-2">';
            
            if (document.documentElement.lang === 'fr') {
                html += 'Cours Réguliers:';
            } else if (document.documentElement.lang === 'ar') {
                html += 'الدورات العادية:';
            } else {
                html += 'Regular Courses:';
            }
            
            html += '</div>';
            
            levelCourses.forEach(course => {
                const isChecked = selectedRegularCourseIds.includes(course.id);
                html += `
                    <div class="flex items-start mb-2">
                        <input type="checkbox" id="course_${course.id}" name="course_ids[]" 
                               value="${course.id}" class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                               onchange="updateSelectedCourses()" ${isChecked ? 'checked' : ''}>
                        <label for="course_${course.id}" class="ml-2 block text-sm flex-1 flex justify-between items-center">
                            <span>${course.matiere}</span>
                            <span class="text-blue-600 font-medium">${course.prix} DH</span>
                        </label>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        // Communication Courses Section - Show only level-specific communication courses
        if (levelCommCourses.length > 0) {
            html += '<div><div class="font-medium text-gray-800 mb-2">';
            
            if (document.documentElement.lang === 'fr') {
                html += 'Cours de Communication:';
            } else if (document.documentElement.lang === 'ar') {
                html += 'دورات التواصل:';
            } else {
                html += 'Communication Courses:';
            }
            
            html += '</div>';
            
            levelCommCourses.forEach(course => {
                const isChecked = selectedCommCourseIds.includes(course.id);
                html += `
                    <div class="flex items-start mb-2">
                        <input type="checkbox" id="comm_course_${course.id}" name="comm_course_ids[]" 
                               value="${course.id}" class="mt-1 h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                               onchange="updateSelectedCourses()" ${isChecked ? 'checked' : ''}>
                        <label for="comm_course_${course.id}" class="ml-2 block text-sm flex-1 flex justify-between items-center">
                            <span>${course.matiere} <span class="text-xs text-gray-500">
                                ${course.niveau_scolaire === 'all' ? 
                                    (document.documentElement.lang === 'fr' 
                                        ? '(Tous niveaux)' 
                                        : document.documentElement.lang === 'ar' 
                                            ? '(جميع المستويات)' 
                                            : '(All levels)') : ''}
                            </span></span>
                            <span class="text-purple-600 font-medium">${course.prix} DH</span>
                        </label>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        coursesContainer.innerHTML = html;
        updateSelectedCourses();
    }
    
    function updateSelectedCourses() {
        const selectedRegularCheckboxes = document.querySelectorAll('input[name="course_ids[]"]:checked');
        const selectedCommCheckboxes = document.querySelectorAll('input[name="comm_course_ids[]"]:checked');
        
        if (selectedRegularCheckboxes.length === 0 && selectedCommCheckboxes.length === 0) {
            selectedCoursesDiv.innerHTML = '<div class="text-gray-500 text-sm">No courses selected</div>';
            paidAmountInput.value = '0.00';
            return;
        }
        
        const selectedRegularCourses = Array.from(selectedRegularCheckboxes).map(checkbox => {
            const courseId = parseInt(checkbox.value);
            return courses.find(course => course.id === courseId);
        });
        
        const selectedCommCourses = Array.from(selectedCommCheckboxes).map(checkbox => {
            const courseId = parseInt(checkbox.value);
            return communicationCourses.find(course => course.id === courseId);
        });
        
        let basePrice = 0;
        let html = '<div class="space-y-2 mb-4">';
        
        if (selectedRegularCourses.length > 0) {
            if (document.documentElement.lang === 'fr') {
                html += '<div class="font-medium text-gray-700">Cours Réguliers:</div>';
            } else if (document.documentElement.lang === 'ar') {
                html += '<div class="font-medium text-gray-700">الدورات العادية:</div>';
            } else {
                html += '<div class="font-medium text-gray-700">Regular Courses:</div>';
            }
            
            selectedRegularCourses.forEach(course => {
                basePrice += parseFloat(course.prix);
                html += `
                    <div class="flex justify-between items-center">
                        <span class="text-gray-800">${course.matiere}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${course.prix} DH
                        </span>
                    </div>
                `;
            });
        }
        
        if (selectedCommCourses.length > 0) {
            if (document.documentElement.lang === 'fr') {
                html += '<div class="font-medium text-gray-700 mt-3">Cours de Communication:</div>';
            } else if (document.documentElement.lang === 'ar') {
                html += '<div class="font-medium text-gray-700 mt-3">دورات التواصل:</div>';
            } else {
                html += '<div class="font-medium text-gray-700 mt-3">Communication Courses:</div>';
            }
            
            selectedCommCourses.forEach(course => {
                basePrice += parseFloat(course.prix);
                html += `
                    <div class="flex justify-between items-center">
                        <span class="text-gray-800">${course.matiere}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            ${course.prix} DH
                        </span>
                    </div>
                `;
            });
        }
        
        const months = parseInt(monthsInput.value) || 1;
        const totalPrice = basePrice * months;
        
        html += '</div>';
        html += `
            <div class="mt-3 pt-3 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="font-medium">
                        ${document.documentElement.lang === 'fr' ? 'Prix Mensuel de Base:' : 
                          document.documentElement.lang === 'ar' ? 'السعر الشهري الأساسي:' : 
                          'Base Monthly Price:'}
                    </span>
                    <span class="font-medium">${basePrice.toFixed(2)} DH</span>
                </div>
                <div class="flex justify-between items-center mt-1 text-blue-600 font-bold">
                    <span>
                        ${document.documentElement.lang === 'fr' ? `Total pour ${months} mois:` : 
                          document.documentElement.lang === 'ar' ? `إجمالي لمدة ${months} شهر:` : 
                          `Total for ${months} month(s):`}
                    </span>
                    <span>${totalPrice.toFixed(2)} DH</span>
                </div>
            </div>
        `;
        
        selectedCoursesDiv.innerHTML = html;
        paidAmountInput.value = totalPrice.toFixed(2);
    }
    
    // Initialize on load
    niveauSelect.addEventListener('change', updateCourses);
    monthsInput.addEventListener('change', function() {
        updateSelectedCourses();
        updatePaymentExpiry();
    });
    
    const enrollmentDateInput = document.getElementById('enrollment_date');
    const paymentExpiryInput = document.getElementById('payment_expiry');
    
    function updatePaymentExpiry() {
        // Get enrollment date
        const enrollmentDate = new Date(enrollmentDateInput.value);
        if (!enrollmentDate || isNaN(enrollmentDate.getTime())) {
            return; // Invalid date
        }
        
        // Get months
        const months = parseInt(monthsInput.value) || 1;
        
        // Calculate expiry date (add months to enrollment date)
        const expiryDate = new Date(enrollmentDate);
        expiryDate.setMonth(expiryDate.getMonth() + months);
        
        // Format date as YYYY-MM-DD
        const year = expiryDate.getFullYear();
        const month = String(expiryDate.getMonth() + 1).padStart(2, '0');
        const day = String(expiryDate.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${day}`;
        
        // Update payment expiry field
        paymentExpiryInput.value = formattedDate;
    }
    
    enrollmentDateInput.addEventListener('change', updatePaymentExpiry);
    
    // Add event listener for form submission to log values
    document.getElementById('studentForm').addEventListener('submit', function(e) {
        console.log('Submitting form with dates:', {
            enrollmentDate: enrollmentDateInput.value,
            paymentExpiry: paymentExpiryInput.value
        });
    });
    
    // Load courses based on initial level
    updateCourses();
    
    // Calculate initial payment expiry based on current values
    updatePaymentExpiry();
    
    // Make updateSelectedCourses accessible globally
    window.updateSelectedCourses = updateSelectedCourses;
});
</script>
@endsection 