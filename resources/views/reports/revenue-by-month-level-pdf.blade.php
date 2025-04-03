<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue by Month and Level - {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1, h2 {
            text-align: center;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 16px;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .header {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .totals-row {
            font-weight: bold;
            background-color: #e6e6e6;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Revenue by Month and Level - {{ $year }}</h1>
        <p>Generated on: {{ $date }}</p>
    </div>
    
    <h2>Monthly Revenue Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Student Count</th>
                <th>Revenue (DH)</th>
            </tr>
        </thead>
        <tbody>
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
                    <td>{{ $data['month'] }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>{{ number_format($data['revenue'], 2) }} DH</td>
                </tr>
            @endforeach
            
            <tr class="totals-row">
                <td>TOTAL</td>
                <td>{{ $totalStudents }}</td>
                <td>{{ number_format($totalRevenue, 2) }} DH</td>
            </tr>
        </tbody>
    </table>
    
    <h2>Revenue by Level and Month</h2>
    @foreach($levelData as $level)
    <h3>{{ $level['label'] }}</h3>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Student Count</th>
                <th>Revenue (DH)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $levelTotalRevenue = 0;
                $levelTotalStudents = 0;
            @endphp
            
            @foreach($monthlyData as $data)
                @php
                    $monthName = $data['month'];
                    $monthRevenue = $level['months'][$monthName]['revenue'] ?? 0;
                    $monthCount = $level['months'][$monthName]['count'] ?? 0;
                    $levelTotalRevenue += $monthRevenue;
                    $levelTotalStudents += $monthCount;
                @endphp
                <tr>
                    <td>{{ $monthName }}</td>
                    <td>{{ $monthCount }}</td>
                    <td>{{ number_format($monthRevenue, 2) }} DH</td>
                </tr>
            @endforeach
            
            <tr class="totals-row">
                <td>TOTAL</td>
                <td>{{ $levelTotalStudents }}</td>
                <td>{{ number_format($levelTotalRevenue, 2) }} DH</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    
    <div class="footer">
        <p>Generated on {{ $date }} - Excellence Riad</p>
    </div>
</body>
</html> 