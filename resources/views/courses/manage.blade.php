@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Courses</h1>
        {{-- <div class="flex space-x-4">
            <a href="{{ route('courses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Add Course
            </a>
            <a href="/courses/create" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Direct Link
            </a>
        </div> --}}
    </div> 

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    {{-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th> --}}
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($regularCourses as $course)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $course->matiere }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($course->niveau_scolaire) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($course->prix, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Regular</td>
                    {{-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('courses.enrollments', $course->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-users"></i>
                        </a>
                        <a href="{{ route('courses.edit', $course->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            modifier 
                        </a>
                        <a href="{{ route('courses.delete', $course->id) }}" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this course?')">
                        supprimer
                        </a>
                    </td> --}}
                </tr>
                @endforeach
                
                @foreach($communicationCourses as $course)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $course->matiere }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($course->niveau_scolaire) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($course->prix, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Communication</td>
                    {{-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('communication-courses.enrollments', $course->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-users"></i>
                        </a>
                        <a href="{{ route('communication-courses.edit', $course->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('communication-courses.delete', $course->id) }}" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this course?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td> --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Course Modal -->
{{-- <div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCourseModalLabel">{{ __('Add New Course') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCourseForm" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="matiere" class="form-label">{{ __('Subject') }}</label>
                        <select class="form-select" id="matiere" name="matiere" required>
                            <option value="Mathématiques">{{ __('Mathématiques') }}</option>
                            <option value="Physique">{{ __('Physique') }}</option>
                            <option value="SVT">{{ __('SVT') }}</option>
                            <option value="Français">{{ __('Français') }}</option>
                            <option value="Anglais">{{ __('Anglais') }}</option>
                           
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="niveau_scolaire" class="form-label">{{ __('School Level') }}</label>
                        <select class="form-select" id="niveau_scolaire" name="niveau_scolaire" required>
                            <option value="premiere_school">{{ __('Première School') }}</option>
                            <option value="1ac">{{ __('1AC') }}</option>
                            <option value="2ac">{{ __('2AC') }}</option>
                            <option value="3ac">{{ __('3AC') }}</option>
                            <option value="tronc_commun">{{ __('Tronc Commun') }}</option>
                            <option value="deuxieme_annee">{{ __('2ème Année') }}</option>
                            <option value="bac">{{ __('BAC') }}</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="prix" class="form-label">{{ __('Price') }}</label>
                        <input type="number" class="form-control" id="prix" name="prix" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="type" class="form-label">{{ __('Type') }}</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="regular">{{ __('Regular') }}</option>
                            <option value="communication">{{ __('Communication') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" form="addCourseForm" class="btn btn-primary">{{ __('Add Course') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel">{{ __('Edit Course') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCourseForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group mb-3">
                        <label for="edit_matiere" class="form-label">{{ __('Subject') }}</label>
                        <select class="form-select" id="edit_matiere" name="matiere" required>
                            <option value="Mathématiques">{{ __('Mathématiques') }}</option>
                            <option value="Physique">{{ __('Physique') }}</option>
                            <option value="SVT">{{ __('SVT') }}</option>
                            <option value="Français">{{ __('Français') }}</option>
                            <option value="Anglais">{{ __('Anglais') }}</option>
                            <option value="Histoire-Géographie">{{ __('Histoire-Géographie') }}</option>
                            <option value="Arabe">{{ __('Arabe') }}</option>
                            <option value="Islamique">{{ __('Islamique') }}</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_niveau_scolaire" class="form-label">{{ __('School Level') }}</label>
                        <select class="form-select" id="edit_niveau_scolaire" name="niveau_scolaire" required>
                            <option value="premiere_school">{{ __('Première School') }}</option>
                            <option value="1ac">{{ __('1AC') }}</option>
                            <option value="2ac">{{ __('2AC') }}</option>
                            <option value="3ac">{{ __('3AC') }}</option>
                            <option value="tronc_commun">{{ __('Tronc Commun') }}</option>
                            <option value="deuxieme_annee">{{ __('2ème Année') }}</option>
                            <option value="bac">{{ __('BAC') }}</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_prix" class="form-label">{{ __('Price') }}</label>
                        <input type="number" class="form-control" id="edit_prix" name="prix" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_type" class="form-label">{{ __('Type') }}</label>
                        <select class="form-select" id="edit_type" name="type" required>
                            <option value="regular">{{ __('Regular') }}</option>
                            <option value="communication">{{ __('Communication') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" form="editCourseForm" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCourseModal" tabindex="-1" role="dialog" aria-labelledby="deleteCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCourseModalLabel">{{ __('Delete Course') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to delete this course?') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form id="deleteCourseForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

@push('scripts')
<script>
    function showAddCourseModal() {
        const modal = new bootstrap.Modal(document.getElementById('addCourseModal'));
        modal.show();
    }

    function showEditCourseModal(id, matiere, niveau_scolaire, prix, type) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_matiere').value = matiere;
        document.getElementById('edit_niveau_scolaire').value = niveau_scolaire;
        document.getElementById('edit_prix').value = prix;
        document.getElementById('edit_type').value = type;
        
        const form = document.getElementById('editCourseForm');
        if (type === 'regular') {
            form.action = '/courses/update/' + id;
        } else {
            form.action = '/communication-courses/update/' + id;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('editCourseModal'));
        modal.show();
    }

    function deleteCourse(id, type) {
        const form = document.getElementById('deleteCourseForm');
        if (type === 'regular') {
            form.action = '/courses/destroy/' + id;
        } else {
            form.action = '/communication-courses/destroy/' + id;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('deleteCourseModal'));
        modal.show();
    }

    // Set default prices based on school level
    document.getElementById('niveau_scolaire').addEventListener('change', function() {
        const level = this.value;
        const priceInput = document.getElementById('prix');
        
        switch(level) {
            case 'premiere_school':
            case '1ac':
            case '2ac':
                priceInput.value = '100';
                break;
            case '3ac':
                priceInput.value = '130';
                break;
            case 'tronc_commun':
                priceInput.value = '150';
                break;
            case 'deuxieme_annee':
            case 'bac':
                priceInput.value = '150';
                break;
        }
    });

    // Set the form action based on course type
    document.getElementById('type').addEventListener('change', function() {
        const type = this.value;
        const form = document.getElementById('addCourseForm');
        
        if (type === 'regular') {
            form.action = '/courses/store';
        } else {
            form.action = '/communication-courses/store';
        }
    });
</script>
@endpush
@endsection 