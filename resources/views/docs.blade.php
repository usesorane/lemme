<!DOCTYPE html>
<html lang="en" class="antialiased bg-white dark:bg-zinc-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page['title'] }} - {{ $siteTitle }}</title>
    <meta name="description" content="{{ $siteDescription }}">
    
    <!-- Tailwind CSS 4 (compiled) -->
    <link rel="stylesheet" href="{{ asset('vendor/lemme/app.css') }}">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body>
    <div class="isolate">
        <div class="fixed inset-x-0 top-0 z-10 border-b border-gray-950/5 dark:border-white/10">
            <div class="h-14">
                TOP NAVIGATION
            </div>
        </div>
        <div class="grid min-h-dvh grid-cols-1 grid-rows-[1fr_1px_auto_1px_auto] pt-26.25 lg:grid-cols-[var(--container-2xs)_2.5rem_minmax(0,1fr)_2.5rem] lg:pt-14.25 xl:grid-cols-[var(--container-2xs)_2.5rem_minmax(0,1fr)_2.5rem]">
            <div class="relative col-start-1 row-span-full row-start-1 max-lg:hidden">
                <div class="absolute inset-0">
                    <div class="sticky top-14.25 bottom-0 left-0 h-full overflow-y-auto p-6 border-r border-zinc-900/10 dark:border-white/10 max-h-[calc(100dvh-(var(--spacing)*14.25))]">
                        <nav>
                            <ul>
                                @foreach ($navigation as $item)
                                    <li class="relative flex items-center">
                                        <!-- Left border (not rounded) -->
                                        <div class="w-[1px] self-stretch mr-0.5 rounded {{ $page['slug'] === $item['slug'] ? 'bg-red-500' : 'bg-transparent' }}"></div>
                                        <!-- Background and content (rounded) -->
                                        <a class="w-full flex justify-between gap-2 py-1 pr-3 text-sm pl-4 rounded-lg text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white {{ $page['slug'] === $item['slug'] ? 'bg-zinc-800/2.5 dark:bg-white/2.5' : 'hover:bg-zinc-800/2.5 dark:hover:bg-white/2.5' }}" 
                                            type="button"
                                            href="{{ $item['url'] }}">
                                            <span class="truncate">{{ $item['title'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="relative row-start-1 grid grid-cols-subgrid lg:col-start-3">
                <div hidden=""></div>
                <div class="mx-auto grid w-full max-w-2xl grid-cols-1 gap-10 xl:max-w-5xl xl:grid-cols-[minmax(0,1fr)_var(--container-2xs)]">
                    <x-markdown class="px-4 pt-10 pb-24 sm:px-6 xl:pr-0 prose dark:prose-invert">
                        {!! $page['raw_content'] !!}
                    </x-markdown>
                    <div class="max-xl:hidden">
                        <div class="sticky top-14 max-h-[calc(100svh-3.5rem)] overflow-x-hidden px-6 pt-10 pb-24">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">On this page</h3>
                            <nav class="space-y-1">
                                @if (isset($page['headings']) && count($page['headings']) > 0)
                                    @foreach($page['headings'] as $heading)
                                        <a href="#{{ $heading['id'] }}" 
                                            class="block py-1 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 {{ $heading['class'] }}">
                                            {{ $heading['text'] }}
                                        </a>
                                    @endforeach
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Smooth scrolling for anchor links
        document.addEventListener('click', function(e) {
            if (e.target.matches('a[href^="#"]')) {
                e.preventDefault();
                const targetId = e.target.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    </script>
</body>
</html>
