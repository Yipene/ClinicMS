<x-clinic-layout>
    <x-slot name="header">Nouvelle vente</x-slot>
    <x-slot name="subheader">Enregistrement caisse — {{ $clinic->name }}</x-slot>

    @php
        $productOptions = $products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price' => (float) $p->sale_price,
            'stock' => $p->stock_quantity,
        ]);
    @endphp

    <form method="POST" action="{{ route('caisse.sales.store') }}"
          x-data="saleForm({{ $productOptions->toJson() }})"
          @submit="prepareSubmit"
          class="grid gap-6 lg:grid-cols-3">
        @csrf

        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium">Patient (optionnel)</label>
                        <select name="patient_id" class="mt-1 w-full rounded-lg border-slate-200">
                            <option value="">— Aucun —</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>{{ $patient->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Module</label>
                        <select name="module" class="mt-1 w-full rounded-lg border-slate-200">
                            @foreach(['caisse','pharmacie','consultation','examen'] as $mod)
                                <option value="{{ $mod }}" @selected(old('module', 'caisse') === $mod)>{{ ucfirst($mod) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <h3 class="font-semibold">Lignes de vente</h3>
                    <button type="button" @click="addLine()" class="text-sm text-teal-600 font-medium">+ Ligne</button>
                </div>

                <template x-for="(line, index) in lines" :key="index">
                    <div class="grid gap-2 sm:grid-cols-12 items-end border-b border-slate-100 pb-3">
                        <div class="sm:col-span-4">
                            <label class="text-xs text-slate-500">Produit stock</label>
                            <select @change="pickProduct(index, $event)" class="w-full rounded-lg border-slate-200 text-sm">
                                <option value="">— Manuel —</option>
                                <template x-for="p in products" :key="p.id">
                                    <option :value="p.id" x-text="p.name + ' (stock: ' + p.stock + ')'"></option>
                                </template>
                            </select>
                            <input type="hidden" :name="'items['+index+'][product_id]'" x-model="line.product_id">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="text-xs text-slate-500">Description *</label>
                            <input type="text" :name="'items['+index+'][description]'" x-model="line.description" required class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs text-slate-500">Qté</label>
                            <input type="number" :name="'items['+index+'][quantity]'" x-model.number="line.quantity" min="1" required class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs text-slate-500">P.U.</label>
                            <input type="number" :name="'items['+index+'][unit_price]'" x-model.number="line.unit_price" min="0" step="1" required class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                    </div>
                </template>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold">Paiements</h3>
                    <button type="button" @click="addPayment()" class="text-sm text-teal-600 font-medium">+ Paiement</button>
                </div>
                <template x-for="(pay, index) in payments" :key="'p'+index">
                    <div class="grid gap-2 sm:grid-cols-3 mb-3">
                        <select :name="'payments['+index+'][method]'" x-model="pay.method" class="rounded-lg border-slate-200 text-sm">
                            <option value="cash">Espèces</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="transfer">Virement</option>
                        </select>
                        <input type="number" :name="'payments['+index+'][amount]'" x-model.number="pay.amount" min="0" step="1" placeholder="Montant" class="rounded-lg border-slate-200 text-sm">
                        <input type="text" :name="'payments['+index+'][reference]'" x-model="pay.reference" placeholder="Réf. transaction" class="rounded-lg border-slate-200 text-sm">
                    </div>
                </template>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl bg-slate-900 text-white p-6 shadow-lg sticky top-24">
                <p class="text-slate-400 text-sm">Sous-total</p>
                <p class="text-2xl font-bold" x-text="formatMoney(subtotal)"></p>
                <div class="mt-4">
                    <label class="text-sm text-slate-400">Remise</label>
                    <input type="number" name="discount" x-model.number="discount" min="0" step="1" class="mt-1 w-full rounded-lg bg-slate-800 border-slate-700 text-white">
                </div>
                <hr class="my-4 border-slate-700">
                <p class="text-slate-400 text-sm">Total à payer</p>
                <p class="text-3xl font-bold text-teal-400" x-text="formatMoney(total)"></p>
                <button type="button" @click="fillPayment()" class="mt-2 text-xs text-teal-300 underline">Remplir paiement = total</button>
                <button type="submit" class="mt-6 w-full rounded-xl bg-teal-500 py-3 font-semibold hover:bg-teal-400 transition">
                    Enregistrer la vente
                </button>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Notes</label>
                <textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border-slate-200 text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>
    </form>

    <script>
        function saleForm(products) {
            return {
                products,
                lines: [{ product_id: '', description: '', quantity: 1, unit_price: 0 }],
                payments: [{ method: 'cash', amount: 0, reference: '' }],
                discount: 0,
                get subtotal() {
                    return this.lines.reduce((s, l) => s + (l.quantity * l.unit_price), 0);
                },
                get total() {
                    return Math.max(0, this.subtotal - this.discount);
                },
                addLine() { this.lines.push({ product_id: '', description: '', quantity: 1, unit_price: 0 }); },
                addPayment() { this.payments.push({ method: 'cash', amount: 0, reference: '' }); },
                pickProduct(index, e) {
                    const p = this.products.find(x => x.id == e.target.value);
                    if (p) {
                        this.lines[index].product_id = p.id;
                        this.lines[index].description = p.name;
                        this.lines[index].unit_price = p.price;
                    } else {
                        this.lines[index].product_id = '';
                    }
                },
                fillPayment() {
                    if (this.payments.length) this.payments[0].amount = this.total;
                },
                formatMoney(v) {
                    return new Intl.NumberFormat('fr-FR').format(v) + ' {{ $clinic->currency }}';
                },
                prepareSubmit() {
                    if (this.payments.reduce((s,p) => s + Number(p.amount), 0) <= 0) {
                        this.fillPayment();
                    }
                }
            };
        }
    </script>
</x-clinic-layout>
