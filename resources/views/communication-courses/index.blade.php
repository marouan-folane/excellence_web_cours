@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Communication Courses</h1>
        @auth
            <a href="{{ route('communication-courses.manage') }}" class="btn btn-primary">
                <i class="fas fa-cog"></i> Manage Courses
            </a>
        @endauth
    </div>

    @foreach($courses as $niveau_scolaire => $levelCourses)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ ucfirst(str_replace('_', ' ', $niveau_scolaire)) }}</h6>
            <a href="{{ route('communication-courses.level', $niveau_scolaire) }}" class="btn btn-sm btn-info">
                View Details
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Students</th>
                            <th>Price</th>
                            <th>Total Revenue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($levelCourses as $course)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $course->matiere)) }}</td>
                            <td>{{ $course->total_students ?? 0 }}</td>
                            <td>{{ number_format($course->prix, 2) }} DH</td>
                            <td>{{ number_format(($course->total_students ?? 0) * $course->prix, 2) }} DH</td>
                            <td>
                                @auth
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#enrollModal{{ $course->id }}">
                                        Enroll Students
                                    </button>
                                @endauth
                            </td>
                        </tr>

                        <!-- Enroll Modal -->
                        <div class="modal fade" id="enrollModal{{ $course->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Enroll Students - {{ ucfirst(str_replace('_', ' ', $course->matiere)) }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('communication-courses.enroll') }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                                            <input type="hidden" name="niveau_scolaire" value="{{ $niveau_scolaire }}">
                                            
                                            <div class="mb-3">
                                                <label for="student_count" class="form-label">Number of Students</label>
                                                <input type="number" class="form-control" id="student_count" name="student_count" min="1" required>
                                            </div>

                                            <div class="mb-3">
                                                <p class="mb-1">Course Details:</p>
                                                <ul class="list-unstyled">
                                                    <li>Level: {{ ucfirst(str_replace('_', ' ', $niveau_scolaire)) }}</li>
                                                    <li>Price per Student: {{ number_format($course->prix, 2) }} DH</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Enroll</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif
@endsection 