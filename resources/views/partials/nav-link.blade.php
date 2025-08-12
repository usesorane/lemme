{{-- 
Parameters:
- $href: string - the link URL
- $title: string - the link text
- $active: bool (optional, defaults to false) - whether the link is active
- $showActiveIndicator: bool (optional, defaults to true) - whether to show a left border on active links
- $class: string (optional, additional classes)
--}}

@php
$isActive = $active ?? false;
$showIndicator = $showActiveIndicator ?? true;
$additionalClass = $class ?? '';
@endphp

<li class="relative flex items-center">
    @if ($showIndicator)
        <!-- Left border -->
        <div class="h-6 w-[1px] mr-0.5 rounded {{ $isActive ? 'bg-pink-400/90' : 'bg-transparent' }}"></div>
    @endif
    <!-- Link-->
    <a class="w-full flex justify-between gap-2 py-1 pr-3 text-sm pl-4 rounded-lg text-zinc-600 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white aria-[current]:text-zinc-950 dark:aria-[current]:text-white text-base/8 sm:text-sm/7 aria-[current]:font-semibold transition-colors duration-150 {{ $isActive ? 'bg-zinc-800/2.5 dark:bg-white/2.5' : 'hover:bg-zinc-800/2.5 dark:hover:bg-white/2.5' }} {{ $additionalClass }}" 
        href="{{ $href }}"
        @if ($isActive) aria-current="page" @endif>
        <span class="truncate">{{ $title }}</span>
    </a>
</li>
