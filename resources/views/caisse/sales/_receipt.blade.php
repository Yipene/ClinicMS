<div id="receipt" class="max-w-lg mx-auto rounded-2xl bg-white border border-slate-200 p-8 shadow-sm print:shadow-none print:border-0">
    <div class="text-center border-b border-slate-200 pb-4 mb-4">
        <h2 class="text-xl font-bold">{{ $clinic->name }}</h2>
        @if($clinic->address)<p class="text-xs text-slate-500">{{ $clinic->address }}</p>@endif
        @if($clinic->phone_primary)<p class="text-xs text-slate-500">{{ $clinic->phone_primary }}</p>@endif
    </div>
    <div class="text-sm space-y-1 mb-4">
        <p><span class="text-slate-500">Reçu N°</span> <strong>{{ $sale->reference }}</strong></p>
        <p><span class="text-slate-500">Date</span> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
        <p><span class="text-slate-500">Caissier</span> {{ $sale->cashier->name }}</p>
        @if($sale->patient)
            <p><span class="text-slate-500">Patient</span> {{ $sale->patient->full_name }}</p>
        @elseif($sale->customer_name)
            <p><span class="text-slate-500">Client</span> {{ $sale->customer_name }}</p>
            @if($sale->customer_phone)<p><span class="text-slate-500">Téléphone</span> {{ $sale->customer_phone }}</p>@endif
        @else
            <p><span class="text-slate-500">Client</span> Comptoir anonyme</p>
        @endif
    </div>
    <table class="w-full text-sm mb-4">
        <thead class="border-b"><tr class="text-slate-500 text-left"><th class="py-2">Désignation</th><th class="py-2 text-center">Qté</th><th class="py-2 text-right">Montant</th></tr></thead>
        <tbody>@foreach($sale->items as $item)<tr class="border-b border-slate-50"><td class="py-2">{{ $item->description }}</td><td class="py-2 text-center">{{ $item->quantity }}</td><td class="py-2 text-right">{{ format_money($item->line_total) }}</td></tr>@endforeach</tbody>
    </table>
    <div class="text-sm border-t pt-4 space-y-1">
        <div class="flex justify-between"><span>Sous-total</span><span>{{ format_money($sale->subtotal) }}</span></div>
        @if($sale->discount > 0)<div class="flex justify-between text-rose-600"><span>Remise</span><span>- {{ format_money($sale->discount) }}</span></div>@endif
        <div class="flex justify-between text-lg font-bold"><span>TOTAL</span><span>{{ format_money($sale->total) }}</span></div>
    </div>
    <div class="mt-4 pt-4 border-t text-sm">
        @php $labels = ['cash'=>'Espèces','mobile_money'=>'Mobile Money','transfer'=>'Virement']; @endphp
        @foreach($sale->payments as $payment)<p>{{ $labels[$payment->method] ?? $payment->method }} : {{ format_money($payment->amount) }}</p>@endforeach
    </div>
    <p class="text-center text-xs text-slate-400 mt-8">Merci de votre confiance</p>
</div>
<style>@media print{body *{visibility:hidden}#receipt,#receipt *{visibility:visible}#receipt{position:absolute;left:0;top:0;width:100%}}</style>
