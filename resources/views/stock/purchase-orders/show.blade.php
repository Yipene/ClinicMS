<x-clinic-layout>
    <x-slot name="header">Bon {{ $order->reference }}</x-slot>
    <x-slot name="subheader">{{ $order->supplier->name }} — {{ $order->status }}</x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
            <h3 class="font-semibold mb-4">Détail commande</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Commandé le</dt><dd>{{ $order->ordered_at?->format('d/m/Y H:i') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Livraison prévue</dt><dd>{{ $order->expected_at?->format('d/m/Y') ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Reçu le</dt><dd>{{ $order->received_at?->format('d/m/Y H:i') ?? '—' }}</dd></div>
            </dl>
            <table class="w-full text-sm mt-6">
                <thead class="text-slate-500 text-left border-b">
                    <tr><th class="py-2">Produit</th><th>Commandé</th><th>Reçu</th><th>PA</th></tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b border-slate-50">
                            <td class="py-2">{{ $item->product->name }}</td>
                            <td>{{ $item->quantity_ordered }}</td>
                            <td>{{ $item->quantity_received }}</td>
                            <td>{{ format_money($item->unit_price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(in_array($order->status, ['ordered', 'partial']))
            <div class="rounded-2xl bg-white border border-teal-200 p-6 shadow-sm">
                <h3 class="font-semibold text-teal-800 mb-4">Réception marchandise</h3>
                <form method="POST" action="{{ route('stock.purchase-orders.receive', $order) }}" class="space-y-4">
                    @csrf
                    @foreach($order->items as $item)
                        @php $remaining = $item->quantity_ordered - $item->quantity_received; @endphp
                        @if($remaining > 0)
                            <div>
                                <label class="text-sm font-medium">{{ $item->product->name }}</label>
                                <p class="text-xs text-slate-500">Reste à recevoir : {{ $remaining }}</p>
                                <input type="number" name="quantities[{{ $item->id }}]" min="0" max="{{ $remaining }}" value="{{ $remaining }}"
                                       class="mt-1 w-full rounded-lg border-slate-200">
                            </div>
                        @endif
                    @endforeach
                    <button type="submit" class="w-full rounded-xl bg-teal-600 py-2.5 text-sm font-medium text-white hover:bg-teal-700">
                        Valider la réception
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="mt-6">
        <x-btn-secondary href="{{ route('stock.purchase-orders.index') }}">← Retour</x-btn-secondary>
    </div>
</x-clinic-layout>
