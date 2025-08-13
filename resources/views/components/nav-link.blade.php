<li class="relative flex items-center" 
    data-slot="control"
    x-data="{ active: {{ $active ? 'true' : 'false' }} }"
    x-on:click="active = true"
>
    @if ($showActiveIndicator)
        <div class="h-6 w-[1px] mr-0.5 rounded {{ $active ? 'bg-pink-400/90' : 'bg-transparent' }}"
            data-slot="indicator"
            :class="{ 'bg-pink-400/90': active, 'bg-transparent': !active }"
        ></div>
    @endif
    <a {{ $attributes->merge(['class' => "w-full flex justify-between gap-2 py-1 pr-3 text-sm pl-4 rounded-lg text-zinc-600 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white aria-[current]:text-zinc-950 dark:aria-[current]:text-white text-base/8 sm:text-sm/7 aria-[current]:font-semibold transition-colors duration-150 " . ($active ? $activeClasses : $inactiveClasses)]) }}
        data-slot="link"
        href="{{ $href }}"
        @if ($active) aria-current="page" @endif
        :class="{ '{{ $activeClasses }}': active, '{{ $inactiveClasses }}': !active }"
    >
        <span class="truncate">{{ $title }}</span>
    </a>
</li>
