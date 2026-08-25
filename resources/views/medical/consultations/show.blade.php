<x-clinic-layout>
    <x-slot name="header">Consultation — {{ $consultation->patient->full_name }}</x-slot>
    <div class="max-w-2xl rounded-2xl bg-white border p-6 shadow-sm text-sm space-y-4">
        <p><strong>Date :</strong> {{ $consultation->consulted_at->format('d/m/Y H:i') }}</p>
        <p><strong>Médecin :</strong> {{ $consultation->doctor->name }}</p>
        <p><strong>Motif :</strong> {{ $consultation->reason ?? '—' }}</p>
        <p><strong>Diagnostic :</strong> {{ $consultation->diagnosis ?? '—' }}</p>
        <p><strong>Prescription :</strong><br>{{ $consultation->prescription ?? '—' }}</p>
        <p><strong>Honoraires :</strong> {{ format_money($consultation->fee) }}</p>
        @if($consultation->sale)<p><a href="{{ route('caisse.sales.show', $consultation->sale) }}" class="text-teal-600">Facture {{ $consultation->sale->reference }}</a></p>@endif
    </div>
</x-clinic-layout>
