<x-clinic-layout>
    <x-slot name="header">Mouvements de stock</x-slot>
    <x-slot name="subheader">Historique des entrées et sorties</x-slot>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <select name="type" class="rounded-lg border-slate-200 text-sm">
            <option value="">Tous les types</option>
            @foreach($typeLabels as $key => $label)
                <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-slate-200 text-sm">
        <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm text-white">Filtrer</button>
    </form>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Produit</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Qté</th>
                    <th class="px-4 py-3">Utilisateur</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($movements as $m)
                    <tr>
                        <td class="px-4 py-3">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $m->product->name }}</td>
                        <td class="px-4 py-3">{{ $typeLabels[$m->type] ?? $m->type }}</td>
                        <td class="px-4 py-3 font-medium {{ $m->quantity < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                        </td>
                        <td class="px-4 py-3">{{ $m->user->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Aucun mouvement.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $movements->links() }}</div>
</x-clinic-layout>
