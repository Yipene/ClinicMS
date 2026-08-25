@php $isEdit = $supplier->exists; @endphp
<x-clinic-layout>
    <x-slot name="header">{{ $isEdit ? 'Modifier le fournisseur' : 'Nouveau fournisseur' }}</x-slot>

    <form method="POST" action="{{ $isEdit ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}"
          class="max-w-xl space-y-4 rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-slate-700">Nom *</label>
            <input name="name" value="{{ old('name', $supplier->name) }}" required class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Contact</label>
            <input name="contact_name" value="{{ old('contact_name', $supplier->contact_name) }}" class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">Téléphone</label>
                <input name="phone" value="{{ old('phone', $supplier->phone) }}" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Adresse</label>
            <textarea name="address" rows="2" class="mt-1 w-full rounded-lg border-slate-200">{{ old('address', $supplier->address) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Délai de livraison (jours) *</label>
            <input type="number" name="delivery_delay_days" value="{{ old('delivery_delay_days', $supplier->delivery_delay_days ?? 7) }}" min="1" required class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $supplier->is_active ?? true)) class="rounded border-slate-300">
            Fournisseur actif
        </label>

        <div class="flex gap-3">
            <x-btn-primary type="submit">Enregistrer</x-btn-primary>
            <x-btn-secondary href="{{ route('admin.suppliers.index') }}">Annuler</x-btn-secondary>
        </div>
    </form>
</x-clinic-layout>
