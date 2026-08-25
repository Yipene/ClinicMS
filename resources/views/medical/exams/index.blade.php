<x-clinic-layout>
    <x-slot name="header">Examens</x-slot>
    <x-btn-primary href="{{ route('medical.exams.create') }}" class="mb-6">+ Prescrire un examen</x-btn-primary>
    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Patient</th><th class="px-4 py-3">Examen</th><th class="px-4 py-3">Statut</th><th></th></tr></thead>
            <tbody class="divide-y">
                @foreach($exams as $e)
                    <tr><td class="px-4 py-3">{{ $e->prescribed_at->format('d/m/Y') }}</td><td class="px-4 py-3">{{ $e->patient->full_name }}</td><td class="px-4 py-3">{{ $e->label }}</td>
                        <td class="px-4 py-3"><span class="text-xs bg-slate-100 px-2 rounded">{{ $e->status }}</span></td>
                        <td class="px-4 py-3"><a href="{{ route('medical.exams.edit', $e) }}" class="text-teal-600">Résultat</a></td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $exams->links() }}</div>
</x-clinic-layout>
