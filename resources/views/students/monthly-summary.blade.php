@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0 text-gray-800">Monthly Revenue Summary by School Level</h1>
                        <a href="{{ route('students.index') }}" class="btn btn-secondary">Back to Students</a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($summaryByLevel->isEmpty())
                        <div class="alert alert-info">
                            No enrollment data found. Please enroll students to view the summary.
                        </div>
                    @else
                        <div class="table-responsive mb-4">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>School Level</th>
                                        <th>Number of Enrollments</th>
                                        <th class="text-end">Monthly Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summaryByLevel as $level)
                                        <tr>
                                            <td>{{ $level->niveau_scolaire }}</td>
                                            <td>{{ $level->enrollment_count }}</td>
                                            <td class="text-end">{{ number_format($level->total_price, 2) }} DH</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-primary fw-bold">
                                        <td>Total</td>
                                        <td>{{ $summaryByLevel->sum('enrollment_count') }}</td>
                                        <td class="text-end">{{ number_format($totalMonthlyRevenue, 2) }} DH</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-xl-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-primary text-white py-3">
                                        <h5 class="mb-0">Monthly Revenue Breakdown</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="revenueChart" width="400" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-success text-white py-3">
                                        <h5 class="mb-0">Summary Statistics</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped table-hover">
                                            <tr>
                                                <th>Total Monthly Revenue:</th>
                                                <td class="text-end">{{ number_format($totalMonthlyRevenue, 2) }} DH</td>
                                            </tr>
                                            <tr>
                                                <th>Average Revenue per Level:</th>
                                                <td class="text-end">
                                                    {{ $summaryByLevel->count() > 0 ? number_format($totalMonthlyRevenue / $summaryByLevel->count(), 2) : 0 }} DH
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Highest Revenue Level:</th>
                                                <td class="text-end">
                                                    @if($summaryByLevel->isNotEmpty())
                                                        @php
                                                            $highestLevel = $summaryByLevel->sortByDesc('total_price')->first();
                                                        @endphp
                                                        {{ $highestLevel->niveau_scolaire }} ({{ number_format($highestLevel->total_price, 2) }} DH)
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Total Active Levels:</th>
                                                <td class="text-end">{{ $summaryByLevel->count() }}</td>
                                            </tr>
                                        </table>
                                        
                                        <div class="d-grid gap-2 mt-4">
                                            <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                                            <a href="{{ route('students.create') }}" class="btn btn-success">Add New Student</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($summaryByLevel->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart data
        const levels = @json($summaryByLevel->pluck('niveau_scolaire'));
        const revenues = @json($summaryByLevel->pluck('total_price'));
        
        // Create color array
        const colors = [
            'rgba(54, 162, 235, 0.8)', 
            'rgba(255, 99, 132, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 205, 86, 0.8)',
            'rgba(201, 203, 207, 0.8)'
        ];
        
        // Get the context of the canvas element
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Create the chart
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: levels,
                datasets: [{
                    label: 'Monthly Revenue (DH)',
                    data: revenues,
                    backgroundColor: colors.slice(0, levels.length),
                    borderColor: colors.slice(0, levels.length).map(color => color.replace('0.8', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (DH)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'School Level'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Monthly Revenue by School Level'
                    }
                }
            }
        });
    });
</script>
@endif
@endsection 