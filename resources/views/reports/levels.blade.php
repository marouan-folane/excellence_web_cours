@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Revenus Par Niveau
            @elseif(session('locale') == 'ar')
                الإيرادات حسب المستوى
            @else
                Revenue By Level
            @endif
        </h1>
        
        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            @if(session('locale') == 'fr')
                Retour aux Rapports
            @elseif(session('locale') == 'ar')
                العودة إلى التقارير
            @else
                Back to Reports
            @endif
        </a>
    </div>
    
    <!-- Revenue By Level Chart -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            @if(session('locale') == 'fr')
                Graphique des Revenus Par Niveau
            @elseif(session('locale') == 'ar')
                رسم بياني للإيرادات حسب المستوى
            @else
                Level Revenue Chart
            @endif
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="h-80">
                <canvas id="levelRevenueChart"></canvas>
            </div>
            
            <div class="h-80">
                <canvas id="levelStudentCountChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Revenue By Level Table -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            @if(session('locale') == 'fr')
                Détails des Revenus Par Niveau
            @elseif(session('locale') == 'ar')
                تفاصيل الإيرادات حسب المستوى
            @else
                Level Revenue Details
            @endif
        </h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">
                            @if(session('locale') == 'fr')
                                Niveau
                            @elseif(session('locale') == 'ar')
                                المستوى
                            @else
                                Level
                            @endif
                        </th>
                        <th class="py-2 px-4 text-center text-sm font-semibold text-gray-600">
                            @if(session('locale') == 'fr')
                                Nombre d'étudiants
                            @elseif(session('locale') == 'ar')
                                عدد الطلاب
                            @else
                                Student Count
                            @endif
                        </th>
                        <th class="py-2 px-4 text-right text-sm font-semibold text-gray-600">
                            @if(session('locale') == 'fr')
                                Revenu
                            @elseif(session('locale') == 'ar')
                                الإيرادات
                            @else
                                Revenue
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $totalRevenue = 0;
                        $totalStudents = 0;
                    @endphp
                    
                    @foreach($revenueByLevel as $level)
                        @php
                            $totalRevenue += $level['revenue'];
                            $totalStudents += $level['count'];
                        @endphp
                        <tr>
                            <td class="py-3 px-4 text-sm text-gray-800">{{ $level['level'] }}</td>
                            <td class="py-3 px-4 text-center text-sm text-gray-800">{{ $level['count'] }}</td>
                            <td class="py-3 px-4 text-right text-sm text-gray-800">{{ number_format($level['revenue'], 2) }} DH</td>
                        </tr>
                    @endforeach
                    
                    <!-- Total Row -->
                    <tr class="bg-gray-50 font-semibold">
                        <td class="py-3 px-4 text-sm text-gray-800">
                            @if(session('locale') == 'fr')
                                TOTAL
                            @elseif(session('locale') == 'ar')
                                المجموع
                            @else
                                TOTAL
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-800">{{ $totalStudents }}</td>
                        <td class="py-3 px-4 text-right text-sm text-gray-800">{{ number_format($totalRevenue, 2) }} DH</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Level Revenue Chart
        const levelCtx = document.getElementById('levelRevenueChart').getContext('2d');
        const levelRevenueChart = new Chart(levelCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_column($revenueByLevel, 'level')) !!},
                datasets: [{
                    label: 'Revenue (DH)',
                    data: {!! json_encode(array_column($revenueByLevel, 'revenue')) !!},
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Revenue Distribution by Level'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw.toLocaleString() + ' DH';
                            }
                        }
                    }
                }
            }
        });
        
        // Level Student Count Chart
        const countCtx = document.getElementById('levelStudentCountChart').getContext('2d');
        const levelStudentCountChart = new Chart(countCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_column($revenueByLevel, 'level')) !!},
                datasets: [{
                    label: 'Student Count',
                    data: {!! json_encode(array_column($revenueByLevel, 'count')) !!},
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Student Count Distribution by Level'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection 