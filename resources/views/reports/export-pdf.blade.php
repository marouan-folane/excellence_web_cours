<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Students Export - {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .header {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Students Export - {{ $date }}</h1>
        <p>Total Students: {{ $students->count() }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Level</th>
                <th>Course</th>
                <th>Status</th>
                <th>Paid Amount</th>
                <th>Payment Expiry</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->id }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->phone }}</td>
                    <td>{{ $levelLabels[$student->niveau_scolaire] ?? $student->niveau_scolaire }}</td>
                    <td>{{ $student->matiere }}</td>
                    <td>{{ $student->status }}</td>
                    <td>{{ number_format($student->paid_amount, 2) }} DH</td>
                    <td>{{ $student->payment_expiry ? $student->payment_expiry->format('Y-m-d') : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Generated on {{ $date }} - Excellence Riad</p>
    </div>
</body>
</html> 