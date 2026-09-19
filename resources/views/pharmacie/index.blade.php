@php
    $productOptions = $products->map(fn ($p) => [
        'id' => $p->id, 'name' => $p->name, 'dci' => $p->dci,
        'price' => (float) $p->sale_price, 'stock' => $p->stock_quantity,
    ]);

    $oldItems = collect(old('items', [
        ['product_id' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0],
    ]))->map(fn ($i) => [
        'product_id' => $i['product_id'] ?? '',
        'description' => $i['description'] ?? '',
        'quantity' => (int) ($i['quantity'] ?? 1),
        'unit_price' => (float) ($i['unit_price'] ?? 0),
    ])->values();

    $oldPayments = collect(old('payments', [
        ['method' => 'cash', 'amount' => 0, 'reference' => ''],
    ]))->map(fn ($p) => [
        'method' => $p['method'] ?? 'cash',
        'amount' => (float) ($p['amount'] ?? 0),
        'reference' => $p['reference'] ?? '',
    ])->values();

    $oldApproItems = collect(old('items', [
        ['product_id' => '', 'quantity' => 1, 'unit_cost' => 0],
    ]))->map(fn ($i) => [
        'product_id' => $i['product_id'] ?? '',
        'quantity' => (int) ($i['quantity'] ?? 1),
        'unit_cost' => (float) ($i['unit_cost'] ?? 0),
    ])->values();

    $statusStyles = [
        'completed' => ['label' => 'Payée', 'class' => 'bg-emerald-50 text-emerald-700'],
        'pending' => ['label' => 'En attente', 'class' => 'bg-amber-50 text-amber-700'],
        'cancelled' => ['label' => 'Annulée', 'class' => 'bg-rose-50 text-rose-700'],
    ];
@endphp

<x-clinic-layout>
    <x-slot name="header">Pharmacie</x-slot>
    <x-slot name="subheader">Ventes, approvisionnement et annulations</x-slot>

    <div x-data="pharmaciePage()" x-init="init()">

        {{-- ==================== TABLEAU + BARRE D'OUTILS INTÉGRÉE ==================== --}}
        <div class="rounded-lg bg-white border border-slate-200 overflow-hidden">
{{-- Barre d'outils : tout sur une seule ligne --}}
<div class="flex flex-wrap items-center gap-2 border-b border-slate-200 p-4">
    <form method="GET" class="flex flex-1 min-w-[240px] gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Référence, patient…"
            class="flex-1 min-w-[160px] rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
        <input type="date" name="date" value="{{ request('date') }}"
            class="rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
        <button type="submit" class="shrink-0 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
            Filtrer
        </button>
    </form>

    <div class="flex gap-2 shrink-0">
        <button type="button" @click="activeModal = 'sale'"
            class="rounded-md bg-teal-600 px-3 py-2 text-sm font-medium text-white hover:bg-teal-700">
            + Vente
        </button>
        @can('pharmacie.supply')
            <button type="button" @click="activeModal = 'appro'"
                class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                + Approvisionnement
            </button>
        @endcan
    </div>
</div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-left">
                        <tr>
                            <th class="px-4 py-2.5 font-medium">Réf.</th>
                            <th class="px-4 py-2.5 font-medium">Date</th>
                            <th class="px-4 py-2.5 font-medium">Client</th>
                            <th class="px-4 py-2.5 font-medium">Statut</th>
                            <th class="px-4 py-2.5 font-medium text-right">Total</th>
                            <th class="px-4 py-2.5 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sales as $sale)
                            @php
                                $cancelledItemIds = $sale->cancellations
                                    ->flatMap(fn ($c) => $c->items ?? [])
                                    ->pluck('sale_item_id');

                                $cancelPayload = [
                                    'id' => $sale->id,
                                    'reference' => $sale->reference,
                                    'total' => $sale->total,
                                    'items' => $sale->items->map(fn ($i) => [
                                        'id' => $i->id,
                                        'description' => $i->description,
                                        'quantity' => $i->quantity,
                                        'line_total' => $i->line_total,
                                        'already_cancelled' => $cancelledItemIds->contains($i->id),
                                        'old_checked' => false,
                                    ])->values(),
                                ];

                                $statusInfo = $statusStyles[$sale->status] ?? ['label' => $sale->status, 'class' => 'bg-slate-100 text-slate-600'];
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 font-mono text-xs text-slate-500">{{ $sale->reference }}</td>
                                <td class="px-4 py-2.5 text-slate-600">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-2.5">
                                    {{ $sale->patient?->full_name ?? $sale->customer_name ?? 'Comptoir anonyme' }}
                                    @if($sale->customer_phone)
                                        <span class="block text-xs text-slate-400">{{ $sale->customer_phone }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex rounded px-2 py-0.5 text-xs font-medium {{ $statusInfo['class'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right font-medium text-slate-900">{{ format_money($sale->total) }}</td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <a href="{{ route('pharmacie.sales.show', $sale) }}" class="text-teal-600 hover:underline text-xs font-medium">Reçu</a>
                                    @can('pharmacie.cancel')
                                        @if($sale->status !== 'cancelled')
                                            <span class="text-slate-300 mx-1">|</span>
                                            <button type="button"
                                                @click='openCancel(@json($cancelPayload))'
                                                class="text-rose-600 hover:underline text-xs font-medium">
                                                Annuler
                                            </button>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500 text-sm">Aucune vente.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $sales->links() }}
            </div>
        </div>

        {{-- ==================== MODAL : VENTE ==================== --}}
        <div x-show="activeModal === 'sale'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="activeModal === 'sale'" x-transition.opacity @click="activeModal = null" class="fixed inset-0 bg-black/40"></div>

            <div x-show="activeModal === 'sale'" x-transition
                 class="relative flex w-full max-w-4xl max-h-[90vh] flex-col rounded-lg bg-white shadow-xl">

                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5 shrink-0">
                    <h2 class="font-semibold text-slate-900">Vente pharmaceutique</h2>
                    <button type="button" @click="activeModal = null" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('pharmacie.sales.store') }}" @submit="prepareSaleSubmit" class="flex flex-col overflow-hidden flex-1">
                    @csrf
                    <input type="hidden" name="module" value="pharmacie">

                    <div class="grid gap-6 lg:grid-cols-3 overflow-y-auto px-5 py-4">
                        @if ($errors->getBag('sale')->any())
                            <div class="lg:col-span-3 rounded-md bg-rose-50 text-rose-700 text-sm p-3">
                                <ul class="list-disc pl-4">
                                    @foreach ($errors->getBag('sale')->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="lg:col-span-2 space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Bénéficiaire</label>
                                <div class="flex gap-4 text-sm">
                                    <label class="flex items-center gap-1.5"><input type="radio" value="patient" x-model="customerType" name="customer_type" class="text-teal-600 focus:ring-teal-500"> Patient</label>
                                    <label class="flex items-center gap-1.5"><input type="radio" value="external" x-model="customerType" name="customer_type" class="text-teal-600 focus:ring-teal-500"> Client externe</label>
                                    <label class="flex items-center gap-1.5"><input type="radio" value="anonymous" x-model="customerType" name="customer_type" class="text-teal-600 focus:ring-teal-500"> Comptoir anonyme</label>
                                </div>
                                <div x-show="customerType === 'patient'" x-cloak class="mt-3">
                                    <select name="patient_id" class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                        <option value="">— Sélectionner un patient —</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>{{ $patient->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="customerType === 'external'" x-cloak class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" x-bind:required="customerType === 'external'"
                                        class="rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Nom du client">
                                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                        class="rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Téléphone (facultatif)">
                                </div>
                            </div>

                            <div>
                                <input type="search" x-model="search" @input.debounce.300ms="filterProducts()" placeholder="Rechercher un médicament — nom, DCI, code…"
                                    class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="text-sm font-medium text-slate-700">Panier</h3>
                                    <button type="button" @click="addLine()" class="text-sm text-teal-600 hover:underline">+ Ligne</button>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="(line, index) in lines" :key="index">
                                        <div class="grid gap-2 sm:grid-cols-12 items-center">
                                            <div class="sm:col-span-4">
                                                <select @change="pickProduct(index, $event)" x-init="$el.value = line.product_id" class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                                    <option value="">— Choisir —</option>
                                                    <template x-for="p in filteredProducts" :key="p.id">
                                                        <option :value="p.id" :selected="p.id == line.product_id" x-text="p.name + (p.dci ? ' ['+p.dci+']' : '') + ' — stock '+p.stock"></option>
                                                    </template>
                                                </select>
                                                <input type="hidden" :name="'items['+index+'][product_id]'" x-model="line.product_id">
                                            </div>
                                            <div class="sm:col-span-4">
                                                <input type="text" :name="'items['+index+'][description]'" x-model="line.description" required placeholder="Description"
                                                    class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <input type="number" :name="'items['+index+'][quantity]'" x-model.number="line.quantity" min="1" required placeholder="Qté"
                                                    class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                            </div>
                                            <div class="sm:col-span-2 flex gap-1">
                                                <input type="number" :name="'items['+index+'][unit_price]'" x-model.number="line.unit_price" min="0" required placeholder="P.U."
                                                    class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                                <button type="button" x-show="lines.length > 1" @click="lines.splice(index, 1)" class="shrink-0 text-slate-400 hover:text-rose-500">&times;</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-slate-700 mb-2">Paiement</h3>
                                <template x-for="(pay, index) in payments" :key="index">
                                    <div class="grid sm:grid-cols-3 gap-2">
                                        <select :name="'payments['+index+'][method]'" x-model="pay.method" class="rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                            <option value="cash">Espèces</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="transfer">Virement</option>
                                        </select>
                                        <input type="number" :name="'payments['+index+'][amount]'" x-model.number="pay.amount" min="0" placeholder="Montant"
                                            class="rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                        <input type="text" :name="'payments['+index+'][reference]'" x-model="pay.reference" placeholder="Réf."
                                            class="rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="rounded-lg border border-slate-200 p-4 h-fit">
                            <p class="text-slate-500 text-xs">Total à payer</p>
                            <p class="text-2xl font-bold text-slate-900 mt-1" x-text="formatMoney(saleTotal)"></p>
                            <label class="block text-xs text-slate-500 mt-3 mb-1">Remise</label>
                            <input type="number" name="discount" x-model.number="discount" min="0" placeholder="0"
                                class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>
                    </div>

                    <div class="border-t border-slate-200 px-5 py-3.5 shrink-0">
                        <button type="submit" class="w-full rounded-md bg-teal-600 py-2.5 text-sm font-semibold text-white hover:bg-teal-700">
                            Valider la vente
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== MODAL : APPROVISIONNEMENT ==================== --}}
        @can('pharmacie.supply')
        <div x-show="activeModal === 'appro'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="activeModal === 'appro'" x-transition.opacity @click="activeModal = null" class="fixed inset-0 bg-black/40"></div>

            <div x-show="activeModal === 'appro'" x-transition
                 class="relative flex w-full max-w-3xl max-h-[90vh] flex-col rounded-lg bg-white shadow-xl">

                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5 shrink-0">
                    <h2 class="font-semibold text-slate-900">Approvisionnement</h2>
                    <button type="button" @click="activeModal = null" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('pharmacie.appro.store') }}" class="flex flex-col overflow-hidden flex-1">
                    @csrf
                    <div class="space-y-4 overflow-y-auto px-5 py-4">
                        @if ($errors->getBag('appro')->any())
                            <div class="rounded-md bg-rose-50 text-rose-700 text-sm p-3">
                                <ul class="list-disc pl-4">
                                    @foreach ($errors->getBag('appro')->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Fournisseur *</label>
                                <select name="supplier_id" required class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="">— Sélectionner —</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Date prévue</label>
                                <input type="date" name="expected_at" value="{{ old('expected_at') }}" class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">{{ old('notes') }}</textarea>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-sm font-medium text-slate-700">Produits reçus</h3>
                                <button type="button" @click="addApproLine()" class="text-sm text-teal-600 hover:underline">+ Ligne</button>
                            </div>
                            <div class="space-y-2">
                                <template x-for="(line, index) in approLines" :key="index">
                                    <div class="grid gap-2 sm:grid-cols-12 items-center">
                                        <div class="sm:col-span-5">
                                            <select :name="'items['+index+'][product_id]'" x-model="line.product_id" required class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                                <option value="">— Produit —</option>
                                                <template x-for="p in products" :key="p.id">
                                                    <option :value="p.id" x-text="p.name + (p.dci ? ' ['+p.dci+']' : '')"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <input type="number" :name="'items['+index+'][quantity]'" x-model.number="line.quantity" min="1" required placeholder="Quantité"
                                                class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                        </div>
                                        <div class="sm:col-span-4 flex gap-1">
                                            <input type="number" :name="'items['+index+'][unit_cost]'" x-model.number="line.unit_cost" min="0" step="0.01" required placeholder="Prix d'achat unitaire"
                                                class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                            <button type="button" x-show="approLines.length > 1" @click="approLines.splice(index, 1)" class="shrink-0 text-slate-400 hover:text-rose-500">&times;</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 px-5 py-3.5 shrink-0">
                        <button type="submit" class="w-full rounded-md bg-slate-800 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">
                            Enregistrer l'approvisionnement
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endcan

        {{-- ==================== MODAL : ANNULATION ==================== --}}
        @can('pharmacie.cancel')
        <div x-show="activeModal === 'cancel'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="activeModal === 'cancel'" x-transition.opacity @click="activeModal = null" class="fixed inset-0 bg-black/40"></div>

            <div x-show="activeModal === 'cancel' && cancelSale" x-transition
                 class="relative flex w-full max-w-lg max-h-[90vh] flex-col rounded-lg bg-white shadow-xl">

                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5 shrink-0">
                    <h2 class="font-semibold text-slate-900">Annuler <span class="font-mono text-sm text-slate-500" x-text="cancelSale?.reference"></span></h2>
                    <button type="button" @click="activeModal = null" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('pharmacie.cancellations.store') }}" class="flex flex-col overflow-hidden flex-1">
                    @csrf
                    <input type="hidden" name="sale_id" :value="cancelSale?.id">

                    <div class="space-y-4 overflow-y-auto px-5 py-4">
                        @if ($errors->getBag('cancel')->any())
                            <div class="rounded-md bg-rose-50 text-rose-700 text-sm p-3">
                                <ul class="list-disc pl-4">
                                    @foreach ($errors->getBag('cancel')->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <p class="text-sm text-slate-500">Total : <strong class="text-slate-900" x-text="formatMoney(cancelSale?.total ?? 0)"></strong></p>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Type d'annulation</label>
                            <select name="type" x-model="cancelType" class="w-full rounded-md border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                <option value="full">Annulation totale</option>
                                <option value="partial">Annulation partielle</option>
                            </select>
                        </div>

                        <div x-show="cancelType === 'partial'" x-cloak class="space-y-1.5">
                            <p class="text-sm text-slate-500">Lignes à annuler (stock restitué) :</p>
                            <template x-for="item in (cancelSale?.items ?? [])" :key="item.id">
                                <label class="flex items-center gap-2 text-sm" :class="item.already_cancelled ? 'opacity-50' : ''">
                                    <input type="checkbox" name="item_ids[]" :value="item.id"
                                           :checked="item.old_checked" :disabled="item.already_cancelled"
                                           class="rounded text-rose-600 focus:ring-rose-500">
                                    <span x-text="item.description + ' × ' + item.quantity + ' — ' + formatMoney(item.line_total)"></span>
                                </label>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Motif *</label>
                            <textarea name="reason" required rows="3" class="w-full rounded-md border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">{{ old('reason') }}</textarea>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 px-5 py-3.5 shrink-0">
                        <button type="submit" class="w-full rounded-md bg-rose-600 py-2.5 text-sm font-semibold text-white hover:bg-rose-700">
                            Confirmer l'annulation
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endcan
    </div>

    <script>
        function pharmaciePage() {
            return {
                activeModal: null,
                products: @json($productOptions),
                filteredProducts: @json($productOptions),
                search: '',
                customerType: '{{ old('customer_type', 'anonymous') }}',
                lines: @json($oldItems),
                payments: @json($oldPayments),
                discount: {{ (float) old('discount', 0) }},

                approLines: @json($oldApproItems),

                cancelSale: null,
                cancelType: '{{ old('type', 'full') }}',

                get saleSubtotal() { return this.lines.reduce((s, l) => s + (l.quantity || 0) * (l.unit_price || 0), 0); },
                get saleTotal() { return Math.max(0, this.saleSubtotal - (this.discount || 0)); },

                init() {
                    @if ($errors->getBag('sale')->any())
                        this.activeModal = 'sale';
                    @elseif ($errors->getBag('appro')->any())
                        this.activeModal = 'appro';
                    @elseif ($errors->getBag('cancel')->any())
                        this.cancelSale = @json($reopenCancelSale);
                        this.activeModal = 'cancel';
                    @endif
                },

                addLine() { this.lines.push({ product_id: '', description: '', quantity: 1, unit_price: 0 }); },
                filterProducts() {
                    const q = this.search.toLowerCase();
                    this.filteredProducts = q
                        ? this.products.filter(p => p.name.toLowerCase().includes(q) || (p.dci && p.dci.toLowerCase().includes(q)))
                        : this.products;
                },
                pickProduct(i, e) {
                    const p = this.products.find(x => x.id == e.target.value);
                    if (p) { this.lines[i].product_id = p.id; this.lines[i].description = p.name; this.lines[i].unit_price = p.price; }
                },
                prepareSaleSubmit() { if (!this.payments[0].amount) this.payments[0].amount = this.saleTotal; },

                addApproLine() { this.approLines.push({ product_id: '', quantity: 1, unit_cost: 0 }); },

                openCancel(sale) {
                    this.cancelSale = sale;
                    this.cancelType = 'full';
                    this.activeModal = 'cancel';
                },

                formatMoney(v) { return new Intl.NumberFormat('fr-FR').format(v) + ' {{ $clinic->currency }}'; },
            };
        }
    </script>
</x-clinic-layout>