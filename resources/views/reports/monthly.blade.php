@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Rapport Mensuel des Revenus
            @elseif(session('locale') == 'ar')
                التقرير الشهري للإيرادات
            @else
                Monthly Revenue Report
            @endif
        </h1>
        
        <div class="flex space-x-2">
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
    </div>
    
    <!-- Year Selector -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <form action="{{ route('reports.monthly') }}" method="GET" class="flex items-center">
            <label for="year" class="block text-sm font-medium text-gray-700 mr-4">
                @if(session('locale') == 'fr')
                    Sélectionner l'année:
                @elseif(session('locale') == 'ar')
                    اختر السنة:
                @else
                    Select Year:
                @endif
            </label>
            <select name="year" id="year" class="rounded-md border-gray-300 shadow-sm mr-4 py-2 px-3">
                @foreach($years as $yearOption)
                    <option value="{{ $yearOption }}" {{ $yearOption == $year ? 'selected' : '' }}>{{ $yearOption }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                @if(session('locale') == 'fr')
                    Filtrer
                @elseif(session('locale') == 'ar')
                    تصفية
                @else
                    Filter
                @endif
            </button>
        </form>
    </div>
    
    <!-- Monthly Revenue Chart -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            @if(session('locale') == 'fr')
                Revenus par Mois pour {{ $year }}
            @elseif(session('locale') == 'ar')
                الإيرادات الشهرية لعام {{ $year }}
            @else
                Monthly Revenue for {{ $year }}
            @endif
        </h2>
        
        <div class="h-80">
            <canvas id="monthlyRevenueChart"></canvas>
        </div>
    </div>
    
    <!-- Monthly Data Table -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            @if(session('locale') == 'fr')
                Données Mensuelles pour {{ $year }}
            @elseif(session('locale') == 'ar')
                البيانات الشهرية لعام {{ $year }}
            @else
                Monthly Data for {{ $year }}
            @endif
        </h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">
                            @if(session('locale') == 'fr')
                                Mois
                            @elseif(session('locale') == 'ar')
                                الشهر
                            @else
                                Month
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
                    
                    @foreach($monthlyData as $data)
                        @php
                            $totalRevenue += $data['revenue'];
                            $totalStudents += $data['count'];
                        @endphp
                        <tr>
                            <td class="py-3 px-4 text-sm text-gray-800">{{ $data['month'] }}</td>
                            <td class="py-3 px-4 text-center text-sm text-gray-800">{{ $data['count'] }}</td>
                            <td class="py-3 px-4 text-right text-sm text-gray-800">{{ number_format($data['revenue'], 2) }} DH</td>
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
        // Monthly Revenue Chart
        const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        const monthlyRevenueChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
                datasets: [{
                    label: 'Revenue (DH)',
                    data: {!! json_encode(array_column($monthlyData, 'revenue')) !!},
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