<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Recette {{ $date }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <h1>{{ $clinic->name }} — Recette journalière</h1>
    <p>{{ $day->translatedFormat('l d F Y') }}</p>
    <p><strong>Total :</strong> {{ format_money($totalRevenue) }} — <strong>Ventes :</strong> {{ $sales->count() }}</p>
    <h3>Par module</h3>
    <ul>
        @foreach($byModule as $module => $amount)
            <li>{{ $moduleLabels[$module] ?? $module }} : {{ format_money($amount) }}</li>
        @endforeach
    </ul>
    <table>
        <thead>
            <tr><th>Réf.</th><th>Heure</th><th>Module</th><th>Patient</th><th class="right">Total</th></tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->reference }}</td>
                    <td>{{ $sale->created_at->format('H:i') }}</td>
                    <td>{{ $moduleLabels[$sale->module] ?? $sale->module }}</td>
                    <td>{{ $sale->patient?->full_name ?? '—' }}</td>
                    <td style="text-align:right">{{ format_money($sale->total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
