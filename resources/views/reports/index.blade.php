@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(session('locale') == 'fr')
                Rapports Financiers
            @elseif(session('locale') == 'ar')
                التقارير المالية
            @else
                Financial Reports
            @endif
        </h1>
        
        <div class="flex space-x-2">
            <form action="{{ route('export.pdf') }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <select name="pdf_language" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="en">English</option>
                    <option value="fr">French</option>
                    <option value="ar">Arabic</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    @if(session('locale') == 'fr')
                        Exporter tous les étudiants (PDF)
                    @elseif(session('locale') == 'ar')
                        تصدير الطلاب (PDF)
                    @else
                        Export Students (PDF)
                    @endif
                </button>
            </form>
            <a href="{{ route('reports.export.students') }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                @if(session('locale') == 'fr')
                    Exporter Étudiants (CSV)
                @elseif(session('locale') == 'ar')
                    تصدير الطلاب (CSV)
                @else
                    Export Students (CSV)
                @endif
            </a>
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filters</h2>
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4 mb-4">
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Education Level</label>
                <select id="level" name="niveau_scolaire" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Levels</option>
                    <option value="premiere_school" {{ request('niveau_scolaire') == 'premiere_school' ? 'selected' : '' }}>Première School</option>
                    <option value="1ac" {{ request('niveau_scolaire') == '1ac' ? 'selected' : '' }}>1st Middle School</option>
                    <option value="2ac" {{ request('niveau_scolaire') == '2ac' ? 'selected' : '' }}>2nd Middle School</option>
                    <option value="3ac" {{ request('niveau_scolaire') == '3ac' ? 'selected' : '' }}>3AC</option>
                    <option value="tronc_commun" {{ request('niveau_scolaire') == 'tronc_commun' ? 'selected' : '' }}>Tronc Commun</option>
                    <option value="deuxieme_annee" {{ request('niveau_scolaire') == 'deuxieme_annee' ? 'selected' : '' }}>Deuxième Année</option>
                    <option value="bac" {{ request('niveau_scolaire') == 'bac' ? 'selected' : '' }}>Bac</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Apply Filter
                </button>
            </div>
        </form>
        
        <div>
            <h3 class="text-md font-medium text-gray-700 mb-2">Export Students by Level:</h3>
            
            <form action="{{ route('export.pdf') }}" method="POST" class="flex flex-wrap items-center mb-4">
                @csrf
                <div class="mr-2 mb-2">
                    <select name="niveau_scolaire" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="premiere_school">Première School</option>
                        <option value="1ac">1st Middle School</option>
                        <option value="2ac">2nd Middle School</option>
                        <option value="3ac">3AC</option>
                        <option value="tronc_commun">Tronc Commun</option>
                        <option value="deuxieme_annee">Deuxième Année Lycée</option>
                        <option value="bac">Bac</option>
                    </select>
                </div>
                
                <div class="mr-2 mb-2">
                    <select name="pdf_language" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="en">English</option>
                        <option value="fr">French</option>
                        <option value="ar">Arabic</option>
                    </select>
                </div>
                
                <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    Export Level PDF
                </button>
            </form>
        </div>
    </div>
    
    <!-- Revenue Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                @if(session('locale') == 'fr')
                    Revenu Total
                @elseif(session('locale') == 'ar')
                    إجمالي الإيرادات
                @else
                    Total Revenue
                @endif
            </h3>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($totalRevenue, 2) }} DH</p>
        </div>
        
        <!-- Current Month Revenue -->
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                @if(session('locale') == 'fr')
                    Revenu du Mois Actuel
                @elseif(session('locale') == 'ar')
                    إيرادات الشهر الحالي
                @else
                    Current Month Revenue
                @endif
            </h3>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($currentMonthRevenue, 2) }} DH</p>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
        </div>
        
        <!-- Previous Month Revenue -->
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                @if(session('locale') == 'fr')
                    Revenu du Mois Précédent
                @elseif(session('locale') == 'ar')
                    إيرادات الشهر السابق
                @else
                    Previous Month Revenue
                @endif
            </h3>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($previousMonthRevenue, 2) }} DH</p>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->subMonth()->format('F Y') }}</p>
        </div>
    </div>
    
    <!-- Revenue By Month Chart -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Revenus Par Mois
                @elseif(session('locale') == 'ar')
                    الإيرادات الشهرية
                @else
                    Monthly Revenue
                @endif
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('reports.monthly-breakdown') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                    @if(session('locale') == 'fr')
                        Voir la répartition mensuelle
                    @elseif(session('locale') == 'ar')
                        عرض التقسيم الشهري
                    @else
                        View monthly breakdown
                    @endif
                </a>
                <a href="{{ route('reports.monthly') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                    @if(session('locale') == 'fr')
                        Voir le rapport détaillé
                    @elseif(session('locale') == 'ar')
                        عرض التقرير المفصل
                    @else
                        View detailed report
                    @endif
                </a>
            </div>
        </div>
        
        <div class="h-80">
            <canvas id="monthlyRevenueChart"></canvas>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue By Subject -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">
                    @if(session('locale') == 'fr')
                        Revenus Par Matière
                    @elseif(session('locale') == 'ar')
                        الإيرادات حسب المادة
                    @else
                        Revenue By Subject
                    @endif
                </h2>
                <a href="{{ route('reports.subjects') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                    @if(session('locale') == 'fr')
                        Voir le rapport détaillé
                    @elseif(session('locale') == 'ar')
                        عرض التقرير المفصل
                    @else
                        View detailed report
                    @endif
                </a>
            </div>
            
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
                            <th class="py-2 px-4 text-center text-sm font-semibold text-gray-600">
                                @if(session('locale') == 'fr')
                                    Nombre d'étudiants
                                @elseif(session('locale') == 'ar')
                                    عدد الطلاب
                                @else
                                    Students
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
                        @foreach($revenueBySubject as $subject)
                        <tr>
                            <td class="py-3 px-4 text-sm text-gray-800">{{ $subject['subject'] }}</td>
                            <td class="py-3 px-4 text-center text-sm text-gray-800">{{ $subject['total_students'] ?? 0 }}</td>
                            <td class="py-3 px-4 text-right text-sm text-gray-800">{{ number_format($subject['total_revenue'], 2) }} DH</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Revenue By Level -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">
                    @if(session('locale') == 'fr')
                        Revenus Par Niveau
                    @elseif(session('locale') == 'ar')
                        الإيرادات حسب المستوى
                    @else
                        Revenue By Level
                    @endif
                </h2>
                <a href="{{ route('reports.levels') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                    @if(session('locale') == 'fr')
                        Voir le rapport détaillé
                    @elseif(session('locale') == 'ar')
                        عرض التقرير المفصل
                    @else
                        View detailed report
                    @endif
                </a>
            </div>
            
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
                        @foreach($revenueByLevel as $level)
                        <tr>
                            <td class="py-3 px-4 text-sm text-gray-800">{{ $level['level'] }}</td>
                            <td class="py-3 px-4 text-center text-sm text-gray-800">{{ $level['count'] }}</td>
                            <td class="py-3 px-4 text-right text-sm text-gray-800">{{ number_format($level['revenue'], 2) }} DH</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Revenue By Month and Level -->
    <div class="bg-white p-6 rounded-lg shadow-md mt-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                @if(session('locale') == 'fr')
                    Revenus Par Mois et Niveau
                @elseif(session('locale') == 'ar')
                    الإيرادات حسب الشهر والمستوى
                @else
                    Revenue By Month and Level
                @endif
            </h2>
            <a href="{{ route('reports.month-level') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                @if(session('locale') == 'fr')
                    Voir le rapport détaillé
                @elseif(session('locale') == 'ar')
                    عرض التقرير المفصل
                @else
                    View detailed report
                @endif
            </a>
        </div>
        
        <p class="text-gray-600 mb-4">
            @if(session('locale') == 'fr')
                Visualisez les revenus ventilés par mois et par niveau scolaire pour une analyse plus approfondie.
            @elseif(session('locale') == 'ar')
                عرض الإيرادات مقسمة حسب الشهر والمستوى الدراسي لتحليل أكثر تفصيلاً.
            @else
                View revenue broken down by month and educational level for more in-depth analysis.
            @endif
        </p>
        
        <a href="{{ route('reports.month-level') }}" class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            @if(session('locale') == 'fr')
                Accéder au rapport
            @elseif(session('locale') == 'ar')
                الوصول إلى التقرير
            @else
                Access Report
            @endif
        </a>
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
                labels: {!! json_encode(array_column($revenueByMonth, 'month')) !!},
                datasets: [{
                    label: 'Revenue (DH)',
                    data: {!! json_encode(array_column($revenueByMonth, 'revenue')) !!},
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