<x-clinic-layout>
    <x-slot name="header">Reçu pharmacie {{ $sale->reference }}</x-slot>
    <div class="flex gap-3 mb-6 print:hidden">
        <button onclick="window.print()" class="rounded-xl bg-teal-600 px-4 py-2 text-sm text-white">Imprimer</button>
        <x-btn-secondary href="{{ route('pharmacie.sales.index') }}">← Retour</x-btn-secondary>
    </div>
    @include('caisse.sales._receipt')
</x-clinic-layout>
