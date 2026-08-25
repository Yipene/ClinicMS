@php
    $nav = config('clinic.navigation', []);
@endphp

<nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
    @foreach ($nav as $item)
        @php
            $canView = empty($item['permission']) || auth()->user()?->can($item['permission']);
        @endphp
        @continue(! $canView)

        @if (! empty($item['children']))
            <div class="pt-3 pb-1 px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                {{ $item['label'] }}
            </div>
            @foreach ($item['children'] as $child)
                @php
                    $childPerm = $child['permission'] ?? $item['permission'] ?? null;
                    if ($childPerm && ! auth()->user()?->can($childPerm)) { continue; }
                    $active = request()->routeIs($child['route'].'*');
                @endphp
                <a href="{{ Route::has($child['route']) ? route($child['route']) : '#' }}"
                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ $active ? 'bg-teal-600/90 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-white' : 'bg-slate-600' }}"></span>
                    {{ $child['label'] }}
                </a>
            @endforeach
        @else
            @php $active = request()->routeIs($item['route'].'*'); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                {{ $item['label'] }}
            </a>
        @endif
    @endforeach
</nav>
