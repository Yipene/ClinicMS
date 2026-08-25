@php $isEdit = $user->exists; @endphp
<x-clinic-layout>
    <x-slot name="header">{{ $isEdit ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}</x-slot>

    <form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}"
          class="max-w-xl space-y-4 rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-slate-700">Nom *</label>
            <input name="name" value="{{ old('name', $user->name) }}" required class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">E-mail *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Téléphone</label>
            <input name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Rôle *</label>
            <select name="role" required class="mt-1 w-full rounded-lg border-slate-200">
                @foreach($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', $user->roles->first()?->name) === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">{{ $isEdit ? 'Nouveau mot de passe' : 'Mot de passe *' }}</label>
            <input type="password" name="password" {{ $isEdit ? '' : 'required' }} class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true)) class="rounded border-slate-300">
            Compte actif
        </label>

        <div class="flex gap-3">
            <x-btn-primary type="submit">Enregistrer</x-btn-primary>
            <x-btn-secondary href="{{ route('admin.users.index') }}">Annuler</x-btn-secondary>
        </div>
    </form>
</x-clinic-layout>
