<aside class="hidden lg:flex lg:w-72 lg:flex-col bg-slate-900 text-white shrink-0">
    <div class="flex h-16 items-center gap-3 px-6 border-b border-slate-800">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-500 font-bold text-lg">C</div>
        <div>
            <p class="font-semibold leading-tight">{{ config('app.name') }}</p>
            <p class="text-xs text-slate-400">Gestion médicale</p>
        </div>
    </div>

    @include('layouts.partials.sidebar-nav')

    <div class="border-t border-slate-800 p-4 text-xs text-slate-500">
        v1.0 — Mai 2026
    </div>
</aside>

<div x-data="{ open: false }" class="lg:hidden">
    <button @click="open = true" type="button" class="fixed bottom-4 right-4 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-teal-600 text-white shadow-lg">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex">
        <div @click="open = false" class="fixed inset-0 bg-black/50"></div>
        <aside class="relative w-72 flex flex-col bg-slate-900 text-white overflow-y-auto">
            <div class="flex h-16 items-center gap-3 px-6 border-b border-slate-800">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-500 font-bold text-lg">C</div>
                <div>
                    <p class="font-semibold">{{ config('app.name') }}</p>
                    <p class="text-xs text-slate-400">Menu</p>
                </div>
            </div>
            @include('layouts.partials.sidebar-nav')
        </aside>
    </div>
</div>
