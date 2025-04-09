@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Add New Course</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('courses.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="course_type" class="block text-sm font-medium text-gray-700">Course Type</label>
                <select name="course_type" id="course_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="regular">Regular Course</option>
                    <option value="communication">Communication Course</option>
                </select>
            </div>

            <div>
                <label for="matiere" class="block text-sm font-medium text-gray-700">Course Name</label>
                <input type="text" name="matiere" id="matiere" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <div>
                <label for="niveau_scolaire" class="block text-sm font-medium text-gray-700">School Level</label>
                <select name="niveau_scolaire" id="niveau_scolaire" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="premiere_school">Première School</option>
                    <option value="1ac">1st Middle School</option>
                    <option value="2ac">2nd Middle School</option>
                    <option value="3ac">3AC</option>
                    <option value="tronc_commun">Tronc Commun</option>
                    <option value="deuxieme_annee">2ème Année Lycée</option>
                    <option value="bac">Baccalauréat</option>
                </select>
            </div>

            <div>
                <label for="prix" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" name="prix" id="prix" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('courses.manage') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Course
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 