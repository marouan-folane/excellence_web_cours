@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800">Delete Course</h1>
            </div>

            <div class="p-6">
                <p class="text-gray-600 mb-4">Are you sure you want to delete the course "{{ $course->name }}"? This action cannot be undone.</p>

                <div class="bg-gray-50 p-4 rounded-md mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Course Details</h2>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $course->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Level</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($course->level) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($course->price, 2) }} DH</dd>
                        </div>
                    </dl>
                </div>

                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="flex justify-end space-x-4">
                    @csrf
                    @method('DELETE')
                    
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Course
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 