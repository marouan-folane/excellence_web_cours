@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0">{{ $levelName }} Enrollments</h5>
                <div class="text-muted small mt-1">
                    Detailed enrollment information for this level
                </div>
            </div>
            <div>
                <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
                <a href="{{ route('enrollments.create', ['level' => $level]) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i>Add Enrollment
                </a>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="alert alert-info">
                <strong>Total Monthly Revenue: </strong>{{ number_format($totalRevenue, 2) }} DH
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Course</th>
                            <th>Type</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Students</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->course_name }}</td>
                                <td>
                                    @if($enrollment->course_type == 'regular')
                                        <span class="badge bg-secondary">Regular</span>
                                    @else
                                        <span class="badge bg-info">Communication</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($enrollment->price, 2) }} DH</td>
                                <td class="text-end">{{ $enrollment->total_students }}</td>
                                <td class="text-end">{{ number_format($enrollment->total_revenue, 2) }} DH</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted mb-2">No enrollments found for this level</div>
                                    <a href="{{ route('enrollments.create', ['level' => $level]) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle me-1"></i>Create Enrollment
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                <h6>Pricing Information for {{ $levelName }}</h6>
                <div class="small text-muted">
                    @if($level == 'premiere_school')
                        Each regular subject: 100 DH per student<br>
                        Communication courses: 150 DH per student
                    @elseif($level == '2_first_middle_niveau')
                        Each regular subject: 100 DH per student<br>
                        SVT+PC combined: 150 DH per student (special bundle)<br>
                        Communication courses: 150 DH per student
                    @elseif($level == '3ac')
                        Each regular subject: 130 DH per student<br>
                        Communication courses: 150 DH per student
                    @elseif($level == 'high_school')
                        Each regular subject: 150 DH per student<br>
                        Communication courses: 150 DH per student
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 992px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header > div:last-child {
            margin-top: 1rem;
            width: 100%;
        }
        
        .card-header .btn {
            margin-bottom: 0.25rem;
            display: block;
            width: 100%;
        }
    }
    
    @media (min-width: 993px) {
        .btn + .btn {
            margin-left: 0.25rem;
        }
    }
    
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endsection 