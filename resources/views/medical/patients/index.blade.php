<x-clinic-layout>
    <x-slot name="header">Patients</x-slot>
    <x-btn-primary href="{{ route('patients.create') }}" class="mb-6">+ Nouveau patient</x-btn-primary>
    <form method="GET" class="mb-4"><input type="search" name="q" value="{{ request('q') }}" placeholder="Nom, code, téléphone…" class="w-full max-w-md rounded-lg border-slate-200"></form>
    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left"><tr><th class="px-4 py-3">Code</th><th class="px-4 py-3">Nom</th><th class="px-4 py-3">Tél.</th><th></th></tr></thead>
            <tbody class="divide-y">
                @foreach($patients as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $p->code }}</td>
                        <td class="px-4 py-3 font-medium">{{ $p->full_name }}</td>
                        <td class="px-4 py-3">{{ $p->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('patients.show', $p) }}" class="text-teal-600">Dossier</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $patients->links() }}</div>
</x-clinic-layout>
