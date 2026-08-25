<header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 backdrop-blur px-4 sm:px-6 lg:px-8">
    <div>
        @isset($header)
            <h1 class="text-lg font-semibold text-slate-900">{{ $header }}</h1>
        @else
            <h1 class="text-lg font-semibold text-slate-900">{{ config('app.name') }}</h1>
        @endisset
        @isset($subheader)
            <p class="text-sm text-slate-500">{{ $subheader }}</p>
        @endisset
    </div>

    <div class="flex items-center gap-3">
        <span class="hidden sm:inline text-sm text-slate-500">{{ now()->translatedFormat('l d F Y') }}</span>
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-100 text-teal-700 font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden md:inline max-w-[140px] truncate">{{ auth()->user()->name }}</span>
                </button>
            </x-slot>
            <x-slot name="content">
                <div class="px-4 py-2 text-xs text-slate-500 border-b">
                    {{ auth()->user()->getRoleNames()->first() ?? 'Utilisateur' }}
                </div>
                <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Déconnexion
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
