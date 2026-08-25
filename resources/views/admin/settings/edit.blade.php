<x-clinic-layout>
    <x-slot name="header">Paramètres de la clinique</x-slot>
    <x-slot name="subheader">Identité, coordonnées et seuils de caisse</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-teal-50 border border-teal-200 px-4 py-3 text-sm text-teal-900">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}"
          class="max-w-2xl space-y-4 rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700">Nom affiché *</label>
            <input name="name" value="{{ old('name', $clinic->name) }}" required class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Raison sociale</label>
            <input name="legal_name" value="{{ old('legal_name', $clinic->legal_name) }}" class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">Téléphone principal</label>
                <input name="phone_primary" value="{{ old('phone_primary', $clinic->phone_primary) }}" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Téléphone secondaire</label>
                <input name="phone_secondary" value="{{ old('phone_secondary', $clinic->phone_secondary) }}" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Adresse</label>
            <textarea name="address" rows="2" class="mt-1 w-full rounded-lg border-slate-200">{{ old('address', $clinic->address) }}</textarea>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">Ville</label>
                <input name="city" value="{{ old('city', $clinic->city) }}" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $clinic->email) }}" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Site web</label>
            <input name="website" value="{{ old('website', $clinic->website) }}" class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">Devise *</label>
                <input name="currency" value="{{ old('currency', $clinic->currency ?? 'XOF') }}" maxlength="3" required class="mt-1 w-full rounded-lg border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Seuil alerte recette 24h</label>
                <input type="number" name="cash_register_threshold" value="{{ old('cash_register_threshold', $clinic->cash_register_threshold) }}" min="0" step="1" class="mt-1 w-full rounded-lg border-slate-200">
                <p class="text-xs text-slate-500 mt-1">Laisser vide pour désactiver l'alerte.</p>
            </div>
        </div>

        <x-btn-primary type="submit">Enregistrer</x-btn-primary>
    </form>
</x-clinic-layout>
