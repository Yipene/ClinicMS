@php
    $nav = config('clinic.navigation', []);
@endphp

<nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
    @foreach ($nav as $item)
        @php
            $canView = empty($item['children'])
                ? (empty($item['permission']) || auth()->user()?->can($item['permission']))
                : collect($item['children'])->contains(function ($child) use ($item) {
                    $permission = $child['permission'] ?? $item['permission'] ?? null;
                    return empty($permission) || auth()->user()?->can($permission);
                });
        @endphp
        @continue(! $canView)

        @if (! empty($item['children']))
            @php
                // Le groupe s'ouvre automatiquement si une de ses pages est active
                $groupActive = collect($item['children'])->contains(
                    fn ($child) => request()->routeIs($child['route'].'*')
                );
            @endphp

            <div x-data="{ expanded: @js($groupActive) }">
                <button
                    @click="expanded = !expanded"
                    type="button"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500 hover:text-slate-300"
                >
                    <span>{{ $item['label'] }}</span>
                    <svg class="h-3.5 w-3.5 transition-transform" :class="{ 'rotate-180': expanded }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="expanded" x-collapse class="space-y-1 mt-1">
                    @foreach ($item['children'] as $child)
                        @php
                            $childPerm = $child['permission'] ?? $item['permission'] ?? null;
                            if ($childPerm && ! auth()->user()?->can($childPerm)) { continue; }
                            $active = request()->routeIs($child['route'].'*');
                        @endphp
                        <a href="{{ Route::has($child['route']) ? route($child['route']) : '#' }}"
                           @click="$dispatch('sidebar-close')"
                           class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ $active ? 'bg-teal-600/90 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-white' : 'bg-slate-600' }}"></span>
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            @php $active = request()->routeIs($item['route'].'*'); @endphp
            <a href="{{ route($item['route']) }}"
               @click="$dispatch('sidebar-close')"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                {{ $item['label'] }}
            </a>
        @endif
    @endforeach
</nav>