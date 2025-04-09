@extends('layouts.app')

@section('content')
@php
$levelName = '';
if($niveau_scolaire == 'premiere_school') {
    $levelName = 'Première École';
} elseif($niveau_scolaire == '1ac') {
    $levelName = '1ère Année Collège';
} elseif($niveau_scolaire == '2ac') {
    $levelName = '2ème Année Collège';
} elseif($niveau_scolaire == '3ac') {
    $levelName = '3ème Année Collège';
} elseif($niveau_scolaire == 'high_school') {
    $levelName = 'Lycée';
} else {
    $levelName = ucfirst(str_replace('_', ' ', $niveau_scolaire));
}
@endphp

<div class="pricing-header p-3 pb-md-4 mx-auto text-center">
    <h1 class="display-4 fw-normal">{{ $levelName }} Courses</h1>
    <p class="fs-5 text-muted">Enroll students in courses for this level</p>
</div>

<div class="row mb-4">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="my-0 font-weight-normal">Available Courses</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Prix (DH)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regularCourses as $course)
                            <tr>
                                <td>{{ ucfirst($course->matiere) }}</td>
                                <td>{{ $course->prix }}</td>
                                <td>
                                    <form action="{{ route('courses.enroll') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                        <input type="hidden" name="niveau_scolaire" value="{{ $niveau_scolaire }}">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-auto">
                                                <input type="number" class="form-control" name="student_count" value="1" min="1" style="width: 80px;">
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-sm btn-primary">Enroll</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="my-0 font-weight-normal">Communication Courses</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Prix (DH)</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($communicationCourses as $course)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $course->matiere)) }}</td>
                                <td>{{ $course->prix }}</td>
                                <td><span class="badge bg-info">Fixed pricing across all levels</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center mb-5">
    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Back to All Courses</a>
</div>
@endsection 