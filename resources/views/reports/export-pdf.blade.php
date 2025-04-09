<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>Students Export - {{ $date }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .header {
            margin-bottom: 20px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
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
        .amount {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            @if(app()->getLocale() === 'ar')
                تقرير الطلاب {{ isset($selectedLevel) && !empty($selectedLevel) ? '- ' . ($levelLabels[$selectedLevel] ?? $selectedLevel) : '' }} - {{ $date }}
            @elseif(app()->getLocale() === 'fr')
                Rapport des Étudiants {{ isset($selectedLevel) && !empty($selectedLevel) ? '- ' . ($levelLabels[$selectedLevel] ?? $selectedLevel) : '' }} - {{ $date }}
            @else
                Students Export {{ isset($selectedLevel) && !empty($selectedLevel) ? '- ' . ($levelLabels[$selectedLevel] ?? $selectedLevel) : '' }} - {{ $date }}
            @endif
        </h1>
        <p>
            @if(app()->getLocale() === 'ar')
                إجمالي الطلاب: {{ $students->count() }}
            @elseif(app()->getLocale() === 'fr')
                Total des Étudiants: {{ $students->count() }}
            @else
                Total Students: {{ $students->count() }}
            @endif
        </p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>
                    @if(app()->getLocale() === 'ar') رقم @elseif(app()->getLocale() === 'fr') ID @else ID @endif
                </th>
                <th>
                    @if(app()->getLocale() === 'ar') الاسم @elseif(app()->getLocale() === 'fr') Nom @else Name @endif
                </th>
                <th>
                    @if(app()->getLocale() === 'ar') البريد الإلكتروني @elseif(app()->getLocale() === 'fr') Email @else Email @endif
                </th>
                <th>
                    @if(app()->getLocale() === 'ar') الهاتف @elseif(app()->getLocale() === 'fr') Téléphone @else Phone @endif
                </th>
                <th>
                    @if(app()->getLocale() === 'ar') المستوى @elseif(app()->getLocale() === 'fr') Niveau @else Level @endif
                </th>
                <th>
                    @if(app()->getLocale() === 'ar') الدورة @elseif(app()->getLocale() === 'fr') Cours @else Course @endif
                </th>
                <th>
                    @if(app()->getLocale() === 'ar') الحالة @elseif(app()->getLocale() === 'fr') Statut @else Status @endif
                </th>
                <th class="amount">
                    @if(app()->getLocale() === 'ar') المبلغ المدفوع @elseif(app()->getLocale() === 'fr') Montant Payé @else Total Paid @endif
                </th>
                <th class="amount">
                    @if(app()->getLocale() === 'ar') الإيراد الشهري @elseif(app()->getLocale() === 'fr') Revenu Mensuel @else Monthly Revenue @endif
                </th>
                <th>
                    @if(app()->getLocale() === 'ar') تاريخ انتهاء الدفع @elseif(app()->getLocale() === 'fr') Date d'expiration @else Payment Expiry @endif
                </th>
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
                    <td>{{ is_array($student->courses_list) ? implode(', ', $student->courses_list) : $student->matiere }}</td>
                    <td>
                        @if(app()->getLocale() === 'ar')
                            {{ $student->status === 'active' ? 'نشط' : 'غير نشط' }}
                        @elseif(app()->getLocale() === 'fr')
                            {{ $student->status === 'active' ? 'Actif' : 'Inactif' }}
                        @else
                            {{ $student->status }}
                        @endif
                    </td>
                    <td class="amount">{{ number_format($student->paid_amount, 2) }} DH</td>
                    <td class="amount">{{ number_format($student->current_monthly_revenue, 2) }} DH</td>
                    <td>{{ $student->payment_expiry ? $student->payment_expiry->format('Y-m-d') : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>
            @if(app()->getLocale() === 'ar')
                تم إنشاؤه في {{ $date }} - رياض اكسلنس
            @elseif(app()->getLocale() === 'fr')
                Généré le {{ $date }} - Excellence Riad
            @else
                Generated on {{ $date }} - Excellence Riad
            @endif
        </p>
    </div>
</body>
</html> 