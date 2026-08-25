<x-clinic-layout>
    <x-slot name="header">Reçu {{ $sale->reference }}</x-slot>

    <div class="flex gap-3 mb-6 print:hidden">
        <a href="{{ route('caisse.sales.pdf', $sale) }}" class="rounded-xl bg-teal-600 px-4 py-2 text-sm text-white hover:bg-teal-700">PDF</a>
        <button onclick="window.print()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm hover:bg-slate-50">Imprimer</button>
        <x-btn-secondary href="{{ route('caisse.sales.index') }}">← Liste des ventes</x-btn-secondary>
    </div>

    @include('caisse.sales._receipt')
</x-clinic-layout>
