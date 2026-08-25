<x-clinic-layout>
    <x-slot name="header">Annulations — Pharmacie</x-slot>
    <x-btn-primary href="{{ route('pharmacie.cancellations.create') }}" class="mb-6">+ Nouvelle annulation</x-btn-primary>
    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Vente</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Montant</th><th class="px-4 py-3">Motif</th><th class="px-4 py-3">Par</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($cancellations as $c)
                    <tr>
                        <td class="px-4 py-3">{{ $c->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $c->sale->reference }}</td>
                        <td class="px-4 py-3">{{ $c->type }}</td>
                        <td class="px-4 py-3">{{ format_money($c->amount) }}</td>
                        <td class="px-4 py-3 max-w-xs truncate">{{ $c->reason }}</td>
                        <td class="px-4 py-3">{{ $c->user->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Aucune annulation.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $cancellations->links() }}</div>
</x-clinic-layout>
