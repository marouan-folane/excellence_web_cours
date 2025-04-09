<form action="{{ route('courses.update', $course->id) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')
    
    <div>
        <label for="matiere" class="block text-sm font-medium text-gray-700">Matière</label>
        <input type="text" name="matiere" id="matiere" value="{{ old('matiere', $course->matiere) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
    </div>

    <div>
        <label for="niveau_scolaire" class="block text-sm font-medium text-gray-700">Niveau Scolaire</label>
        <input type="text" name="niveau_scolaire" id="niveau_scolaire" value="{{ old('niveau_scolaire', $course->niveau_scolaire) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
    </div>

    <div>
        <label for="prix" class="block text-sm font-medium text-gray-700">Prix</label>
        <input type="number" name="prix" id="prix" value="{{ old('prix', $course->prix) }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
    </div>

    <div>
        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
        <input type="text" name="type" id="type" value="{{ old('type', $course->type) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
    </div>

    <div class="flex justify-end space-x-4">
        <a href="{{ route('courses.manage') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
        </a>
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Update Course
        </button>
    </div>
</form>
```