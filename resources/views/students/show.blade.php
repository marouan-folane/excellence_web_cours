@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800">Student Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('students.receipt', $student) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" target="_blank">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Receipt
                </a>
                <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <div class="p-6">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h2>
                    
                    <div class="divide-y divide-gray-200">
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Name:</span>
                            <span class="text-sm text-gray-900">{{ $student->name ?? 'N/A' }}</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Email:</span>
                            <span class="text-sm text-gray-900">{{ $student->email ?? 'N/A' }}</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Phone:</span>
                            <span class="text-sm text-gray-900">{{ $student->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Parent Name:</span>
                            <span class="text-sm text-gray-900">{{ $student->parent_name ?? 'N/A' }}</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Address:</span>
                            <span class="text-sm text-gray-900">{{ $student->address ?? 'N/A' }}</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Enrollment Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Enrollment Information</h2>
                    
                    <div class="divide-y divide-gray-200">
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">School Level:</span>
                            <span class="text-sm text-gray-900">{{ $student->niveau_scolaire }}</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Subject(s):</span>
                            <span class="text-sm text-gray-900">{{ $student->matiere }}</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Total Price:</span>
                            <span class="text-sm font-medium text-blue-600">{{ $student->total_price ?? 0 }} DH</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Paid Amount:</span>
                            <span class="text-sm font-medium text-green-600">{{ $student->paid_amount ?? 0 }} DH</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Enrollment Duration:</span>
                            <span class="text-sm text-gray-900">{{ $student->months ?? 1 }} month(s)</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Enrollment Date:</span>
                            <span class="text-sm text-gray-900">{{ $student->enrollment_date ? $student->enrollment_date->format('Y-m-d') : 'N/A' }}</span>
                        </div>
                        <div class="py-3 flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Payment Expiry:</span>
                            <span class="text-sm {{ $student->isPaymentExpired() ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                {{ $student->payment_expiry ? $student->payment_expiry->format('Y-m-d') : 'N/A' }}
                                @if($student->payment_expiry)
                                    @if($student->enrollment_date && $student->enrollment_date->gt(now()))
                                        <span class="text-purple-600 font-medium">(Future enrollment - starts in {{ now()->diffInDays($student->enrollment_date) }} days)</span>
                                    @else
                                        ({{ $student->getRemainingDays() }} days remaining)
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Courses -->
            <div class="mt-6 bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Enrolled Courses</h2>
                
                <div class="overflow-x-auto bg-white rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollment Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($student->enrollments as $enrollment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $enrollment->getCourseName() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($enrollment->getCourseType()) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-medium">
                                        {{ $enrollment->getPrice() }} DH
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('Y-m-d') : 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No course enrollments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this student record?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Student
                    </button>
                </form>
                <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Student
                </a>
                <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="d-flex gap-2">
        <a href="{{ route('students.edit', $student) }}" class="btn btn-primary">Edit</a>
        <form action="{{ route('students.receipt', $student->id) }}" method="GET" class="d-inline">
            <div class="d-flex align-items-center">
                <div class="me-2">
                    <select name="pdf_language" class="form-select form-select-sm">
                        <option value="en">English</option>
                        <option value="fr">French</option>
                        <option value="ar">Arabic</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Generate Receipt</button>
            </div>
        </form>
        <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this student?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
@endsection 