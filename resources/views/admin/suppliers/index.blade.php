<x-clinic-layout>
    <x-slot name="header">Fournisseurs</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-teal-50 border border-teal-200 px-4 py-3 text-sm text-teal-900">{{ session('status') }}</div>
    @endif

    <div class="mb-6">
        <x-btn-primary href="{{ route('admin.suppliers.create') }}">+ Nouveau fournisseur</x-btn-primary>
    </div>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Délai (j)</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($suppliers as $supplier)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $supplier->name }}</td>
                        <td class="px-4 py-3">{{ $supplier->phone ?? $supplier->email ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $supplier->delivery_delay_days }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-teal-600 hover:underline">Modifier</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $suppliers->links() }}</div>
</x-clinic-layout>
