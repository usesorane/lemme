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

    <script>!function(){try{var d=document.documentElement,c=d.classList;c.remove('light','dark');var e=localStorage.getItem('theme');if('system'===e||(!e&&true)){var t='(prefers-color-scheme: dark)',m=window.matchMedia(t);if(m.media!==t||m.matches){d.style.colorScheme = 'dark';c.add('dark')}else{d.style.colorScheme = 'light';c.add('light')}}else if(e){c.add(e|| '')}if(e==='light'||e==='dark')d.style.colorScheme=e}catch(e){}}()</script>
</head>
<body>
    <!-- Top Navigation -->
    <div class="fixed inset-x-0 top-0 z-10 border-b border-gray-950/5 dark:border-white/10">
        <div class="flex h-14 items-center justify-between gap-8 px-4 sm:px-6">
            <div class="flex items-center gap-4">
                <a class="shrink-0" aria-label="Home" href="/"><svg viewBox="0 0 167 21" fill="none" class="h-5 text-black dark:text-white"><path class="fill-sky-400" d="M17.183 0C12.6 0 9.737 2.291 8.59 6.873c1.719-2.29 3.723-3.15 6.014-2.577 1.307.326 2.242 1.274 3.275 2.324 1.685 1.71 3.635 3.689 7.894 3.689 4.582 0 7.445-2.291 8.591-6.872-1.718 2.29-3.723 3.15-6.013 2.576-1.308-.326-2.243-1.274-3.276-2.324C23.39 1.98 21.44 0 17.183 0ZM8.59 10.309C4.01 10.309 1.145 12.6 0 17.182c1.718-2.291 3.723-3.15 6.013-2.577 1.308.326 2.243 1.274 3.276 2.324 1.685 1.71 3.635 3.689 7.894 3.689 4.582 0 7.445-2.29 8.59-6.872-1.718 2.29-3.722 3.15-6.013 2.577-1.307-.327-2.242-1.276-3.276-2.325-1.684-1.71-3.634-3.689-7.893-3.689Z"></path><path fill="currentColor" d="M51.547 8.688h-3v5.803c0 1.548 1.016 1.524 3 1.427v2.346c-4.015.483-5.611-.629-5.611-3.773V8.688H43.71V6.172h2.225V2.925l2.612-.774v4.021h2.998v2.516Zm11.43-2.516h2.61v12.092h-2.61v-1.741c-.92 1.28-2.346 2.055-4.233 2.055-3.288 0-6.021-2.78-6.021-6.36 0-3.603 2.733-6.36 6.021-6.36 1.886 0 3.313.774 4.233 2.032V6.172Zm-3.821 9.915c2.176 0 3.82-1.62 3.82-3.87 0-2.248-1.644-3.868-3.82-3.868-2.177 0-3.821 1.62-3.821 3.869s1.644 3.87 3.82 3.87ZM69.94 4.36a1.687 1.687 0 0 1-1.668-1.669c.002-.443.179-.868.491-1.18a1.662 1.662 0 0 1 2.354 0c.312.312.49.737.491 1.18 0 .895-.75 1.669-1.668 1.669Zm-1.306 13.905V6.172h2.612v12.092h-2.612Zm5.635 0V.609h2.611v17.654H74.27ZM93.834 6.172h2.757l-3.797 12.092h-2.563l-2.516-8.15-2.539 8.15h-2.563L78.816 6.172h2.757l2.346 8.343 2.54-8.343h2.49l2.514 8.343 2.37-8.343ZM99.83 4.36c-.92 0-1.669-.774-1.669-1.669.002-.443.18-.868.492-1.18a1.661 1.661 0 0 1 2.354 0c.313.312.49.737.492 1.18 0 .895-.75 1.669-1.669 1.669Zm-1.306 13.905V6.172h2.612v12.092h-2.612ZM110.52 5.858c2.708 0 4.643 1.838 4.643 4.982v7.423h-2.612v-7.158c0-1.838-1.064-2.804-2.708-2.804-1.717 0-3.071 1.015-3.071 3.482v6.48h-2.612V6.174h2.612V7.72c.798-1.257 2.103-1.862 3.748-1.862Zm17.024-4.522h2.612v16.927h-2.612v-1.741c-.918 1.282-2.345 2.055-4.231 2.055-3.289 0-6.022-2.78-6.022-6.36 0-3.603 2.733-6.36 6.022-6.36 1.886 0 3.313.774 4.231 2.032V1.336Zm-3.821 14.751c2.177 0 3.821-1.62 3.821-3.87 0-2.248-1.644-3.868-3.821-3.868-2.176 0-3.82 1.62-3.82 3.869s1.644 3.87 3.82 3.87Zm15.187 2.49c-3.651 0-6.384-2.78-6.384-6.36 0-3.602 2.733-6.359 6.384-6.359 2.37 0 4.426 1.233 5.393 3.12l-2.249 1.306c-.532-1.137-1.717-1.863-3.168-1.863-2.128 0-3.748 1.62-3.748 3.797 0 2.176 1.62 3.797 3.748 3.797 1.451 0 2.636-.75 3.216-1.863l2.249 1.282c-1.015 1.91-3.071 3.144-5.441 3.144Zm9.746-9.068c0 2.201 6.505.87 6.505 5.345 0 2.419-2.104 3.724-4.716 3.724-2.418 0-4.159-1.089-4.933-2.83l2.249-1.305c.387 1.088 1.355 1.74 2.684 1.74 1.161 0 2.056-.386 2.056-1.354 0-2.151-6.505-.942-6.505-5.27 0-2.274 1.959-3.701 4.425-3.701 1.983 0 3.628.92 4.474 2.515l-2.2 1.233c-.436-.943-1.283-1.378-2.274-1.378-.943 0-1.765.41-1.765 1.281Zm11.148 0c0 2.201 6.505.87 6.505 5.345 0 2.419-2.104 3.724-4.716 3.724-2.418 0-4.158-1.089-4.933-2.83l2.249-1.305c.387 1.088 1.354 1.74 2.684 1.74 1.161 0 2.056-.386 2.056-1.354 0-2.151-6.505-.942-6.505-5.27 0-2.274 1.959-3.701 4.426-3.701 1.982 0 3.627.92 4.473 2.515l-2.2 1.233c-.435-.943-1.282-1.378-2.273-1.378-.944 0-1.766.41-1.766 1.281Z"></path></svg></a><button class="flex items-center gap-0.5 rounded-2xl bg-gray-950/5 py-0.5 pr-1.5 pl-2.5 text-xs/5 font-medium text-gray-950 tabular-nums hover:bg-gray-950/7.5 data-active:bg-gray-950/7.5 dark:bg-white/10 dark:text-white dark:hover:bg-white/12.5 dark:data-active:bg-white/12.5" aria-label="Select version of library" id="headlessui-menu-button-«reg»" type="button" aria-haspopup="menu" aria-expanded="false" data-headlessui-state="">v4.1<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon" class="size-4 fill-gray-400"><path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path></svg></button>
            </div>
            <div class="flex items-center gap-6 max-md:hidden">
                <button type="button" 
                    onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');"
                class="flex size-6 items-center justify-center rounded-md transition hover:bg-zinc-900/5 dark:hover:bg-white/5" aria-label="Switch to dark theme"><span class="absolute size-12 pointer-fine:hidden"></span><svg viewBox="0 0 20 20" fill="none" aria-hidden="true" class="h-5 w-5 stroke-zinc-900 dark:hidden"><path d="M12.5 10a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"></path><path stroke-linecap="round" d="M10 5.5v-1M13.182 6.818l.707-.707M14.5 10h1M13.182 13.182l.707.707M10 15.5v-1M6.11 13.889l.708-.707M4.5 10h1M6.11 6.111l.708.707"></path></svg><svg viewBox="0 0 20 20" fill="none" aria-hidden="true" class="hidden h-5 w-5 stroke-white dark:block"><path d="M15.224 11.724a5.5 5.5 0 0 1-6.949-6.949 5.5 5.5 0 1 0 6.949 6.949Z"></path></svg></button>
            </div>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid min-h-screen pt-14 lg:grid-cols-[300px_1fr] xl:grid-cols-[300px_1fr_300px]">
        <!-- Sidebar Navigation -->
        <div class="max-lg:hidden border-r border-zinc-900/10 dark:border-white/10">
            <div class="sticky top-14 h-[calc(100vh-3.5rem)] overflow-y-auto p-6">
                <nav>
                    <ul class="space-y-1">
                        @foreach ($navigation as $item)
                            <li class="relative flex items-center">
                                <!-- Left border (not rounded) -->
                                <div class="w-[1px] self-stretch mr-0.5 rounded {{ $page['slug'] === $item['slug'] ? 'bg-red-500' : 'bg-transparent' }}"></div>
                                <!-- Background and content (rounded) -->
                                <a class="w-full flex justify-between gap-2 py-1 pr-3 text-sm pl-4 rounded-lg text-zinc-600 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white aria-[current]:text-zinc-950 dark:aria-[current]:text-white text-base/8 sm:text-sm/7 aria-[current]:font-semibold {{ $page['slug'] === $item['slug'] ? 'bg-zinc-800/2.5 dark:bg-white/2.5' : 'hover:bg-zinc-800/2.5 dark:hover:bg-white/2.5' }}" 
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

        <!-- Main Content -->
        <div class="mx-auto w-full max-w-2xl lg:max-w-3xl">
            <x-markdown class="px-4 pt-10 pb-24 sm:px-6 xl:pr-0 prose dark:prose-invert">
                {!! $page['raw_content'] !!}
            </x-markdown>
        </div>

        <!-- Table of Contents -->
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
