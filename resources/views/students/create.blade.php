@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800">Add New Student</h1>
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

            <form action="{{ route('students.store') }}" method="POST" id="studentForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h2>
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Student Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="parent_name" class="block text-sm font-medium text-gray-700 mb-1">Parent Name</label>
                            <input type="text" id="parent_name" name="parent_name" value="{{ old('parent_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea id="address" name="address" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
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
                                    <option value="{{ $value }}" {{ old('niveau_scolaire') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Available Courses *</label>
                            <div id="courses_container" class="border border-gray-300 rounded-md p-4 bg-white max-h-56 overflow-y-auto">
                                <div class="text-gray-500 text-sm">Please select a school level first</div>
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
                                <input type="number" id="months" name="months" 
                                       value="{{ old('months', 1) }}" min="1" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <p class="mt-1 text-xs text-gray-500">Payment expiry will be calculated based on this value.</p>
                            </div>
                            
                            <!-- Hidden student_count field, always set to 1 -->
                            <input type="hidden" id="student_count" name="student_count" value="1">
                        </div>
                        
                        <div class="mt-4">
                            <label for="paid_amount" class="block text-sm font-medium text-gray-700 mb-1">Paid Amount (DH) *</label>
                            <input type="number" id="paid_amount" name="paid_amount" 
                                   value="{{ old('paid_amount', '0.00') }}" step="0.01" min="0" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="enrollment_date" class="block text-sm font-medium text-gray-700 mb-1">Enrollment Date *</label>
                                <input type="date" id="enrollment_date" name="enrollment_date" 
                                       value="{{ old('enrollment_date', date('Y-m-d')) }}" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            
                            <div>
                                <label for="payment_expiry" class="block text-sm font-medium text-gray-700 mb-1">Payment Expiry Date *</label>
                                <input type="date" id="payment_expiry" name="payment_expiry" 
                                       value="{{ old('payment_expiry', date('Y-m-d', strtotime('+1 month'))) }}" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-100" readonly required>
                                <p class="mt-1 text-xs text-gray-500">Auto-calculated based on enrollment date and months.</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Create Student
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
    const enrollmentDateInput = document.getElementById('enrollment_date');
    const paymentExpiryInput = document.getElementById('payment_expiry');
    
    // Log debugging info to console
    console.log('Loading course data...');
    
    // All courses data from the server
    const courses = @json($courses);
    const communicationCourses = @json($communicationCourses);
    
    // Log course data for debugging
    console.log('Regular courses:', courses);
    console.log('Communication courses:', communicationCourses);
    
    function updateCourses() {
        const selectedLevel = niveauSelect.value;
        console.log('Selected level:', selectedLevel);
        
        if (!selectedLevel) {
            coursesContainer.innerHTML = '<div class="text-gray-500 text-sm">Please select a school level first</div>';
            return;
        }
        
        // Debug: Log all courses to see what's available
        console.log('All courses array:', courses);
        console.log('All communication courses:', communicationCourses);
        
        // Filter courses for the selected level - ensure exact match for level
        const levelCourses = courses.filter(course => course.niveau_scolaire === selectedLevel);
        const levelCommCourses = communicationCourses.filter(course => course.niveau_scolaire === selectedLevel);
        
        console.log('Filtered courses for level ' + selectedLevel + ':', levelCourses);
        console.log('Filtered communication courses for level ' + selectedLevel + ':', levelCommCourses);
        
        if (levelCourses.length === 0 && levelCommCourses.length === 0) {
            coursesContainer.innerHTML = '<div class="text-gray-500 text-sm">No courses available for this level. Please add courses for ' + selectedLevel + ' first.</div>';
            return;
        }
        
        // Create HTML for regular courses
        let coursesHtml = '';
        if (levelCourses.length > 0) {
            coursesHtml += '<div class="mb-4"><h3 class="text-sm font-medium text-gray-700 mb-2">Regular Courses</h3>';
            levelCourses.forEach(course => {
                coursesHtml += `
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="course_${course.id}" name="course_ids[]" value="${course.id}" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                               onchange="updateSelectedCourses()">
                        <label for="course_${course.id}" class="ml-2 text-sm text-gray-700">
                            ${course.matiere} (${course.prix} DH)
                        </label>
                    </div>
                `;
            });
            coursesHtml += '</div>';
        }
        
        // Create HTML for communication courses
        if (levelCommCourses.length > 0) {
            coursesHtml += '<div class="mb-4"><h3 class="text-sm font-medium text-gray-700 mb-2">Communication Courses</h3>';
            levelCommCourses.forEach(course => {
                coursesHtml += `
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="comm_course_${course.id}" name="comm_course_ids[]" value="${course.id}" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                               onchange="updateSelectedCourses()">
                        <label for="comm_course_${course.id}" class="ml-2 text-sm text-gray-700">
                            ${course.matiere} (${course.prix} DH)
                        </label>
                    </div>
                `;
            });
            coursesHtml += '</div>';
        }
        
        coursesContainer.innerHTML = coursesHtml || '<div class="text-gray-500 text-sm">No courses available for this level</div>';
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
            const courseId = checkbox.value;
            return courses.find(course => course.id.toString() === courseId);
        });
        
        const selectedCommCourses = Array.from(selectedCommCheckboxes).map(checkbox => {
            const courseId = checkbox.value;
            return communicationCourses.find(course => course.id.toString() === courseId);
        });
        
        const studentCount = parseInt(document.getElementById('student_count').value) || 1;
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
                const coursePrice = parseFloat(course.prix);
                const totalCoursePrice = coursePrice * studentCount;
                basePrice += totalCoursePrice;
                html += `
                    <div class="flex justify-between items-center">
                        <span class="text-gray-800">${course.matiere}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${coursePrice} DH × ${studentCount} = ${totalCoursePrice} DH
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
                const coursePrice = parseFloat(course.prix);
                const totalCoursePrice = coursePrice * studentCount;
                basePrice += totalCoursePrice;
                html += `
                    <div class="flex justify-between items-center">
                        <span class="text-gray-800">${course.matiere}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            ${coursePrice} DH × ${studentCount} = ${totalCoursePrice} DH
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
        updatePaymentExpiry();
    }
    
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
        
        // Properly calculate months from the enrollment date
        // This ensures that if enrollment is Sept 15, and months is 3, 
        // the expiry will be Dec 15 (not just adding 90 days)
        const newMonth = expiryDate.getMonth() + months;
        expiryDate.setMonth(newMonth);
        
        // Handle month overflow (e.g., Jan 31 + 1 month should be Feb 28/29)
        // Check if the day of the month is different after setting the new month
        if (expiryDate.getDate() !== enrollmentDate.getDate()) {
            // If different, it means we've crossed into the next month
            // Set the date to the last day of the previous month
            expiryDate.setDate(0);
        }
        
        // Format date as YYYY-MM-DD
        const year = expiryDate.getFullYear();
        const month = String(expiryDate.getMonth() + 1).padStart(2, '0');
        const day = String(expiryDate.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${day}`;
        
        // Update payment expiry field
        paymentExpiryInput.value = formattedDate;
    }
    
    // Initialize on load
    niveauSelect.addEventListener('change', updateCourses);
    monthsInput.addEventListener('change', function() {
        updateSelectedCourses();
        updatePaymentExpiry();
    });
    document.getElementById('student_count').addEventListener('change', updateSelectedCourses);
    enrollmentDateInput.addEventListener('change', updatePaymentExpiry);
    
    // If a level is already selected, load courses
    if (niveauSelect.value) {
        updateCourses();
    }
    
    // Initial calculation
    updatePaymentExpiry();
    
    // Make updateSelectedCourses accessible globally
    window.updateSelectedCourses = updateSelectedCourses;
});
</script>
@endsection 