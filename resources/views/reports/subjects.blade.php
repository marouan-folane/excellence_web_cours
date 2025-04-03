@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Revenus Par Matière
            @elseif(session('locale') == 'ar')
                الإيرادات حسب المادة
            @else
                Revenue By Subject
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
    
    <!-- Revenue By Subject Chart -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            @if(session('locale') == 'fr')
                Graphique des Revenus Par Matière
            @elseif(session('locale') == 'ar')
                رسم بياني للإيرادات حسب المادة
            @else
                Subject Revenue Chart
            @endif
        </h2>
        
        <div class="h-80">
            <canvas id="subjectRevenueChart"></canvas>
        </div>
    </div>
    
    <!-- Revenue By Subject Table -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            @if(session('locale') == 'fr')
                Détails des Revenus Par Matière
            @elseif(session('locale') == 'ar')
                تفاصيل الإيرادات حسب المادة
            @else
                Subject Revenue Details
            @endif
        </h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">
                            @if(session('locale') == 'fr')
                                Matière
                            @elseif(session('locale') == 'ar')
                                المادة
                            @else
                                Subject
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
                    @endphp
                    
                    @foreach($revenueBySubject as $subject)
                        @php
                            $totalRevenue += $subject['revenue'];
                        @endphp
                        <tr>
                            <td class="py-3 px-4 text-sm text-gray-800">{{ $subject['subject'] }}</td>
                            <td class="py-3 px-4 text-right text-sm text-gray-800">{{ number_format($subject['revenue'], 2) }} DH</td>
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
        // Subject Revenue Chart
        const subjectCtx = document.getElementById('subjectRevenueChart').getContext('2d');
        const subjectRevenueChart = new Chart(subjectCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($revenueBySubject, 'subject')) !!},
                datasets: [{
                    label: 'Revenue (DH)',
                    data: {!! json_encode(array_column($revenueBySubject, 'revenue')) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' DH';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toLocaleString() + ' DH';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection 