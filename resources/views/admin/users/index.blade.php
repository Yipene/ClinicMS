<x-clinic-layout>
    <x-slot name="header">Utilisateurs</x-slot>
    <x-slot name="subheader">Comptes et rôles d'accès</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-teal-50 border border-teal-200 px-4 py-3 text-sm text-teal-900">{{ session('status') }}</div>
    @endif

    <div class="mb-6">
        <x-btn-primary href="{{ route('admin.users.create') }}">+ Nouvel utilisateur</x-btn-primary>
    </div>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3">Rôle</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->roles->first()?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $user->is_active ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-teal-600 hover:underline">Modifier</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</x-clinic-layout>
