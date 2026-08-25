<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Recette {{ $month }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <h1>{{ $clinic->name }} — Recette mensuelle</h1>
    <p>{{ $start->translatedFormat('F Y') }}</p>
    <p><strong>Chiffre d'affaires :</strong> {{ format_money($currentTotal) }}</p>
    <table>
        <thead><tr><th>Module</th><th class="right">Montant</th></tr></thead>
        <tbody>
            @foreach($byModule as $row)
                <tr>
                    <td>{{ $moduleLabels[$row->module] ?? $row->module }}</td>
                    <td style="text-align:right">{{ format_money($row->revenue) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
