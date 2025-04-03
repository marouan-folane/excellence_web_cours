@extends('layouts.app')

@section('content')
<div class="pricing-header p-3 pb-md-4 mx-auto text-center">
    <h1 class="display-4 fw-normal">Course Pricing</h1>
    <p class="fs-5 text-muted">View all course pricing by school level</p>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="my-0 font-weight-normal">Regular Courses</h5>
            </div>
            <div class="card-body">
                @foreach($regularCourses as $niveau => $courses)
                <h5>{{ ucfirst(str_replace('_', ' ', $niveau)) }}</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Prix (DH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr>
                                <td>{{ ucfirst($course->matiere) }}</td>
                                <td>{{ $course->prix }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('courses.level', $niveau) }}" class="btn btn-outline-primary mb-4">Enroll Students in {{ ucfirst(str_replace('_', ' ', $niveau)) }}</a>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="my-0 font-weight-normal">Communication Courses</h5>
            </div>
            <div class="card-body">
                @foreach($communicationCourses as $niveau => $courses)
                <h5>{{ ucfirst(str_replace('_', ' ', $niveau)) }}</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Prix (DH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $course->matiere)) }}</td>
                                <td>{{ $course->prix }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection 