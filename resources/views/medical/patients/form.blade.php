@php $isEdit = $patient->exists; @endphp
<x-clinic-layout>
    <x-slot name="header">{{ $isEdit ? 'Modifier patient' : 'Nouveau patient' }}</x-slot>
    <form method="POST" action="{{ $isEdit ? route('patients.update', $patient) : route('patients.store') }}" class="max-w-2xl space-y-4 rounded-2xl bg-white border p-6 shadow-sm">
        @csrf @if($isEdit) @method('PUT') @endif
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Prénom *</label><input name="first_name" value="{{ old('first_name', $patient->first_name) }}" required class="mt-1 w-full rounded-lg border-slate-200"></div>
            <div><label class="text-sm font-medium">Nom *</label><input name="last_name" value="{{ old('last_name', $patient->last_name) }}" required class="mt-1 w-full rounded-lg border-slate-200"></div>
            <div><label class="text-sm font-medium">Téléphone</label><input name="phone" value="{{ old('phone', $patient->phone) }}" class="mt-1 w-full rounded-lg border-slate-200"></div>
            <div><label class="text-sm font-medium">Email</label><input type="email" name="email" value="{{ old('email', $patient->email) }}" class="mt-1 w-full rounded-lg border-slate-200"></div>
            <div><label class="text-sm font-medium">Naissance</label><input type="date" name="birth_date" value="{{ old('birth_date', $patient->birth_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-200"></div>
            <div><label class="text-sm font-medium">Genre</label><select name="gender" class="mt-1 w-full rounded-lg border-slate-200"><option value="">—</option>@foreach(['M','F','other'] as $g)<option value="{{ $g }}" @selected(old('gender',$patient->gender)==$g)>{{ $g }}</option>@endforeach</select></div>
        </div>
        <div><label class="text-sm font-medium">Adresse</label><textarea name="address" class="mt-1 w-full rounded-lg border-slate-200" rows="2">{{ old('address', $patient->address) }}</textarea></div>
        <div><label class="text-sm font-medium">Notes médicales</label><textarea name="notes" class="mt-1 w-full rounded-lg border-slate-200" rows="3">{{ old('notes', $patient->notes) }}</textarea></div>
        <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm text-white">Enregistrer</button>
    </form>
</x-clinic-layout>
