<li class="relative flex items-center"
    data-slot="control"
    x-data="{ active: {{ $active ? 'true' : 'false' }} }"
    @if ($isReactive) x-on:click="active = true" @endif
    @link:active="active = true"
    @link:inactive="active = false"
>
    @if ($showActiveIndicator)
        <div class="h-6 w-[1px] mr-0.5 rounded bg-transparent data-active:bg-lemme-accent/90"
            data-slot="indicator"
            @if ($active) data-active="true" @endif
            :data-active="active"
        ></div>
    @endif
    <a {{ $attributes->merge(['class' => "w-full flex justify-between gap-2 py-1 pr-3 text-sm pl-4 rounded-lg text-zinc-600 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white aria-[current]:text-zinc-950 dark:aria-[current]:text-white text-base/8 sm:text-sm/7 aria-[current]:font-semibold transition-colors duration-150 aria-[current]:bg-zinc-800/2.5 dark:aria-[current]:bg-white/2.5 hover:bg-zinc-800/2.5 dark:hover:bg-white/2.5"]) }}
        data-slot="link"
        href="{{ $href }}"
        @if ($active) aria-current="page" @endif
        :aria-current="active ? 'page' : null"
    >
        <span class="truncate">{{ $title }}</span>
    </a>
</li>
