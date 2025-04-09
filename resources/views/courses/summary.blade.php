@extends('layouts.app')

@section('content')
<div class="pricing-header p-3 pb-md-4 mx-auto text-center">
    <h1 class="display-4 fw-normal">Enrollment Summary</h1>
    <p class="fs-5 text-muted">View all student enrollments and total pricing</p>
</div>

@if($enrollments->isEmpty())
<div class="alert alert-info text-center">
    <h4 class="alert-heading">No enrollments yet!</h4>
    <p>No students have been enrolled in any courses. Please enroll students first.</p>
    <hr>
    <p class="mb-0">
        <a href="{{ route('courses.index') }}" class="btn btn-primary">View Courses</a>
    </p>
</div>
@else
<div class="row mb-4">
    <div class="col-md-10 mx-auto">
        @foreach($enrollments as $niveau => $students)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="my-0 font-weight-normal">
                    @if($niveau == 'premiere_school')
                        Première École
                    @elseif($niveau == '1ac')
                        1ère Année Collège
                    @elseif($niveau == '2ac')
                        2ème Année Collège
                    @elseif($niveau == '3ac')
                        3ème Année Collège
                    @elseif($niveau == 'high_school')
                        Lycée
                    @else
                        {{ ucfirst(str_replace('_', ' ', $niveau)) }}
                    @endif
                </h5>
                <span class="badge bg-success fs-5">Total: {{ number_format($totalPriceByLevel[$niveau], 2) }} DH</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Student Count</th>
                                <th>Price per Student (DH)</th>
                                <th>Total (DH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td>{{ ucfirst($student->course->matiere) }}</td>
                                <td>{{ $student->student_count }}</td>
                                <td>{{ $student->course->prix }}</td>
                                <td>{{ number_format($student->course->prix * $student->student_count, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="my-0 font-weight-normal">Monthly Total Pricing Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>School Level</th>
                                <th>Total Monthly Price (DH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($totalPriceByLevel as $niveau => $totalPrice)
                            <tr>
                                <td>
                                    @if($niveau == 'premiere_school')
                                        Première École
                                    @elseif($niveau == '1ac')
                                        1ère Année Collège
                                    @elseif($niveau == '2ac')
                                        2ème Année Collège
                                    @elseif($niveau == '3ac')
                                        3ème Année Collège
                                    @elseif($niveau == 'high_school')
                                        Lycée
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $niveau)) }}
                                    @endif
                                </td>
                                <td>{{ number_format($totalPrice, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="table-dark">
                                <td><strong>Grand Total</strong></td>
                                <td><strong>{{ number_format(array_sum($totalPriceByLevel), 2) }} DH</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="text-center mb-5">
    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Back to All Courses</a>
</div>
@endsection 