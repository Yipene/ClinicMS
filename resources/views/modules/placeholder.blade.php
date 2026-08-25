<x-clinic-layout>
    <x-slot name="header">{{ $title }}</x-slot>
    <x-slot name="subheader">Module en cours de développement</x-slot>

    <div class="max-w-2xl mx-auto text-center py-16">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-100 text-teal-600 mb-6">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-900">{{ $title }}</h2>
        <p class="mt-3 text-slate-600">
            Cette section sera implémentée dans la prochaine phase de développement.
        </p>
        <a href="{{ route('dashboard') }}" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-teal-700 transition">
            Retour au tableau de bord
        </a>
    </div>
</x-clinic-layout>
