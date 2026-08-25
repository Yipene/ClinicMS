<x-clinic-layout>
    <x-slot name="header">Résultat — {{ $exam->label }}</x-slot>
    <x-slot name="subheader">{{ $exam->patient->full_name }}</x-slot>
    <form method="POST" action="{{ route('medical.exams.update', $exam) }}" class="max-w-xl space-y-4 rounded-2xl bg-white border p-6 shadow-sm">
        @csrf @method('PUT')
        <div><label class="text-sm font-medium">Statut</label><select name="status" class="mt-1 w-full rounded-lg border-slate-200">
            @foreach(['prescribed','in_progress','completed','cancelled'] as $s)<option value="{{ $s }}" @selected($exam->status==$s)>{{ $s }}</option>@endforeach
        </select></div>
        <div><label class="text-sm font-medium">Date résultat</label><input type="date" name="result_at" value="{{ $exam->result_at?->format('Y-m-d') }}" class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div><label class="text-sm font-medium">Résultat</label><textarea name="result" rows="8" class="mt-1 w-full rounded-lg border-slate-200 font-mono text-sm">{{ $exam->result }}</textarea></div>
        <button type="submit" onclick="window.print()" class="mr-2 rounded-xl border px-5 py-2.5 text-sm">Imprimer</button>
        <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm text-white">Enregistrer</button>
    </form>
</x-clinic-layout>
