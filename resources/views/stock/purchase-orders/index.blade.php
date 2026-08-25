<x-clinic-layout>
    <x-slot name="header">Approvisionnements</x-slot>
    <x-slot name="subheader">Bons de commande fournisseurs</x-slot>

    <div class="mb-6">
        <x-btn-primary href="{{ route('stock.purchase-orders.create') }}">+ Nouveau bon de commande</x-btn-primary>
    </div>

    <form method="GET" class="mb-4">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border-slate-200 text-sm">
            <option value="">Tous statuts</option>
            @foreach(['draft','ordered','partial','received','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </form>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Référence</th>
                    <th class="px-4 py-3">Fournisseur</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $order->reference }}</td>
                        <td class="px-4 py-3">{{ $order->supplier->name }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs">{{ $order->status }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $order->ordered_at?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('stock.purchase-orders.show', $order) }}" class="text-teal-600 hover:underline">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Aucun bon de commande.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</x-clinic-layout>
