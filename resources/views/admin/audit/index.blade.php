<x-clinic-layout>
    <x-slot name="header">Journal d'audit</x-slot>
    <x-slot name="subheader">Traçabilité des actions sensibles</x-slot>

    <form method="GET" class="mb-6 flex flex-wrap gap-2">
        <input type="text" name="action" value="{{ request('action') }}" placeholder="Action (ex. sale.created)"
               class="rounded-lg border-slate-200 text-sm">
        <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-slate-200 text-sm">
        <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm text-white">Filtrer</button>
    </form>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Utilisateur</th>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">Détails</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $log->user?->name ?? 'Système' }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $log->action }}</td>
                        <td class="px-4 py-3 text-xs text-slate-600 max-w-md truncate">
                            @if($log->new_values){{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Aucune entrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</x-clinic-layout>
