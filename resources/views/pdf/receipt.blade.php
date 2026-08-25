<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Reçu {{ $sale->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        h2 { margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        th, td { padding: 6px 4px; border-bottom: 1px solid #e2e8f0; }
        th { text-align: left; color: #64748b; font-size: 11px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .total { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    <div style="text-align:center;border-bottom:1px solid #e2e8f0;padding-bottom:12px;margin-bottom:16px;">
        <h2>{{ $clinic->name }}</h2>
        @if($clinic->address)<div style="font-size:10px;color:#64748b;">{{ $clinic->address }}</div>@endif
        @if($clinic->phone_primary)<div style="font-size:10px;color:#64748b;">{{ $clinic->phone_primary }}</div>@endif
    </div>
    <p><strong>Reçu N°</strong> {{ $sale->reference }}</p>
    <p><strong>Date</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>Caissier</strong> {{ $sale->cashier->name }}</p>
    @if($sale->patient)<p><strong>Patient</strong> {{ $sale->patient->full_name }}</p>@endif
    <table>
        <thead><tr><th>Désignation</th><th class="center">Qté</th><th class="right">Montant</th></tr></thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">{{ format_money($item->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="right">Sous-total : {{ format_money($sale->subtotal) }}</p>
    @if($sale->discount > 0)<p class="right">Remise : - {{ format_money($sale->discount) }}</p>@endif
    <p class="right total">TOTAL : {{ format_money($sale->total) }}</p>
    @php $labels = ['cash'=>'Espèces','mobile_money'=>'Mobile Money','transfer'=>'Virement']; @endphp
    @foreach($sale->payments as $payment)
        <p>{{ $labels[$payment->method] ?? $payment->method }} : {{ format_money($payment->amount) }}</p>
    @endforeach
    <p style="text-align:center;margin-top:24px;font-size:10px;color:#94a3b8;">Merci de votre confiance</p>
</body>
</html>
