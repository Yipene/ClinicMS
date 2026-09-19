<x-clinic-layout>
    <x-slot name="header">Annuler une vente</x-slot>

    <form method="GET" class="mb-6 flex gap-2 max-w-md">
        <select name="sale_id" class="flex-1 rounded-lg border-slate-200" onchange="if(this.value) this.form.submit()">
            <option value="">— Choisir une vente —</option>
            @foreach($recentSales as $s)
                <option value="{{ $s->id }}" @selected($sale?->id == $s->id)>{{ $s->reference }} — {{ format_money($s->total) }} ({{ $s->created_at->format('d/m/Y') }})</option>
            @endforeach
        </select>
    </form>

    @if($sale)
        <form method="POST" action="{{ route('pharmacie.cancellations.store') }}" class="max-w-2xl rounded-2xl bg-white border p-6 shadow-sm space-y-4">
            @csrf
            <input type="hidden" name="sale_id" value="{{ $sale->id }}">
            <p class="text-sm">Vente <strong>{{ $sale->reference }}</strong> — Total {{ format_money($sale->total) }}</p>
            <div>
                <label class="block text-sm font-medium">Type d'annulation</label>
                <select name="type" id="cancel-type" class="mt-1 w-full rounded-lg border-slate-200" onchange="document.getElementById('partial-items').classList.toggle('hidden', this.value!=='partial')">
                    <option value="full">Annulation totale</option>
                    <option value="partial">Annulation partielle</option>
                </select>
            </div>
            <div id="partial-items" class="hidden space-y-2">
                <p class="text-sm text-slate-500">Sélectionnez les lignes à annuler (stock restitué) :</p>
                @foreach($sale->items as $item)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" @disabled($sale->cancellations->flatMap(fn ($c) => $c->items ?? [])->pluck('sale_item_id')->contains($item->id))>
                        {{ $item->description }} × {{ $item->quantity }} — {{ format_money($item->line_total) }}
                    </label>
                @endforeach
            </div>
            <div>
                <label class="block text-sm font-medium">Motif *</label>
                <textarea name="reason" required rows="3" class="mt-1 w-full rounded-lg border-slate-200"></textarea>
            </div>
            <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm text-white hover:bg-rose-700">Confirmer l'annulation</button>
        </form>
    @endif
</x-clinic-layout>
