<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement #{{ $receipt_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }
        .receipt-container {
            position: relative;
            border: 1px solid #ddd;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 15px;
            text-align: center;
            border-bottom: 3px solid #f39c12;
        }
        .logo {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .receipt-title {
            font-size: 14pt;
            font-weight: 300;
        }
        .receipt-number {
            font-size: 11pt;
            background: #f39c12;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            display: inline-block;
            margin-top: 5px;
        }
        .content {
            padding: 15px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 12pt;
            color: #2c3e50;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 10px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #7f8c8d;
            margin-bottom: 2px;
            font-size: 9pt;
        }
        .info-value {
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 5px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
        }
        td {
            padding: 5px;
            border-bottom: 1px solid #eee;
            font-size: 9pt;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .amount {
            text-align: right;
            font-weight: 600;
        }
        .total-row {
            background-color: #f1f8ff;
            font-weight: bold;
        }
        .total-row td {
            border-top: 1px solid #bdc3c7;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .signature-box {
            width: 45%;
        }
        .signature-line {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            text-align: center;
            color: #7f8c8d;
            font-size: 8pt;
        }
        .stamp-section {
            position: absolute;
            right: 30px;
            top: 220px;
            width: 100px;
            height: 100px;
            border: 1px dashed #bdc3c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #95a5a6;
            font-style: italic;
            font-size: 8pt;
            transform: rotate(10deg);
        }
        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-regular {
            background-color: #3498db;
            color: white;
        }
        .badge-communication {
            background-color: #2ecc71;
            color: white;
        }
        .footer {
            text-align: center;
            padding: 10px 0;
            color: #7f8c8d;
            font-size: 8pt;
            background-color: #ecf0f1;
            border-top: 1px solid #ddd;
        }
        .footer p {
            margin: 2px 0;
        }
        .date {
            text-align: right;
            font-style: italic;
            color: #95a5a6;
            font-size: 8pt;
            padding: 5px 15px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 40pt;
            color: rgba(0,0,0,0.03);
            white-space: nowrap;
            font-weight: bold;
            z-index: 0;
        }
        .tear-line {
            border-top: 1px dashed #bdc3c7;
            margin: 5px 0;
            position: relative;
        }
        .tear-icon {
            position: absolute;
            right: 10px;
            top: -10px;
            font-size: 8pt;
            color: #95a5a6;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="watermark">Riad Excellence  </div>
        
        <div class="header">
            <div class="logo"> Riad Excellence </div>
            <div class="receipt-title">Reçu de Paiement</div>
            <div class="receipt-number">#{{ $receipt_number }}</div>
            <div class="receipt-date">Date: {{ now()->format('d/m/Y') }}</div>
            <div class="receipt-time">Heure: {{ now()->format('H:i') }}</div>
        </div>
        
        <div class="content">
            <div class="section">
                <h1 class="text-center text-2xl font-bold text-blue-500">Riad excellence</h1>
                <h2 class="section-title">Informations de l'Étudiant</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nom Complet</div>
                        <div class="info-value">{{ $student->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">{{ $student->phone ?? 'Non renseigné' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Niveau Scolaire</div>
                        <div class="info-value">{{ $student->niveau_scolaire }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date d'Expiration</div>
                        <div class="info-value highlight">{{ $student->payment_expiry ? $student->payment_expiry->format('d/m/Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="stamp-section">
                <span>Cachet</span>
            </div>
            
            <div class="section">
                <h2 class="section-title">Cours Inscrits</h2>
                <table>
                    <thead>
                        <tr>
                            <th width="50%">Cours</th>
                            <th width="25%">Type</th>
                            <th width="25%">Prix (DH)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                        <tr>
                            <td>{{ $course->course->name ?? $course->course->matiere }}</td>
                            <td>
                                @if($course->course->type === 'regular')
                                    <span class="badge badge-regular">Régulier</span>
                                @elseif($course->course->type === 'communication')
                                    <span class="badge badge-communication">Com</span>
                                @else
                                    {{ $course->course->type }}
                                @endif
                            </td>
                            <td class="amount">{{ number_format($course->course->prix, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="2">Montant Total Payé</td>
                            <td class="amount">{{ number_format($student->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="2">Revenu Mensuel</td>
                            <td class="amount">{{ number_format($student->current_monthly_revenue, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line">
                        Signature de l'Étudiant
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        Signature de l'Administration
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tear-line">
            <span class="tear-icon">✂</span>
        </div>
        
        <div class="date">
            Généré le: {{ now()->format('d/m/Y à H:i') }}
        </div>
        
        <div class="footer">
            <p>Merci d'avoir choisi Excellence Riad pour votre éducation</p>
            <p>Tél: +212 522 123 456 | Email: contact@excellence-riad.ma</p>
        </div>
    </div>
</body>
</html> 