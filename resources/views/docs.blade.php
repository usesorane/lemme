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
<body x-data="{ searchModalOpen: false }" @keydown.cmd.k.prevent="searchModalOpen = true" @keydown.ctrl.k.prevent="searchModalOpen = true">
    <!-- Top Navigation -->
    <div class="fixed inset-x-0 top-0 z-10 border-b border-gray-950/5 dark:border-white/10">
        <div class="bg-white dark:bg-zinc-900 flex h-14 items-center justify-between gap-8 px-4 sm:px-6">
            <div class="flex-shrink-0">
                <a class="" aria-label="Home" href="/">
                    @include('lemme::partials.logo')
                </a>
            </div>
            <!-- Search Bar -->
            <div class="hidden lg:block lg:max-w-md lg:flex-auto">
                <button type="button" @click="searchModalOpen = true" class="hidden h-8 w-full items-center gap-2 rounded-lg bg-white pr-3 pl-2 text-sm text-zinc-500 ring-1 ring-zinc-900/10 hover:ring-zinc-900/20 lg:flex dark:bg-white/5 dark:text-zinc-400 dark:ring-white/10 dark:ring-inset dark:hover:ring-white/20">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" class="h-5 w-5 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12.01 12a4.25 4.25 0 1 0-6.02-6 4.25 4.25 0 0 0 6.02 6Zm0 0 3.24 3.25"></path>
                    </svg>Find something...<kbd class="ml-auto text-2xs text-zinc-400 dark:text-zinc-500"><kbd class="font-sans">⌘</kbd><kbd class="font-sans">K</kbd></kbd>
                </button>
            </div>
            <!-- Theme Switcher -->
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
                            @if ($item['type'] === 'page')
                                @include('lemme::partials.nav-link', [
                                    'href' => $item['url'],
                                    'title' => $item['title'],
                                    'active' => $page['slug'] === $item['slug'],
                                    'showActiveIndicator' => true
                                ])
                            @elseif ($item['type'] === 'group')
                                @include('lemme::partials.nav-group', [
                                    'group' => $item,
                                    'currentPageSlug' => $page['slug']
                                ])
                            @endif
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mx-auto w-full max-w-2xl lg:max-w-3xl">
            <div class="px-4 pt-10 pb-24 sm:px-6 xl:pr-0 prose dark:prose-invert">
                {!! $html !!}
            </div>
        </div>

        <!-- Table of Contents -->
        <div class="max-xl:hidden">
            <div class="sticky top-14 max-h-[calc(100svh-3.5rem)] overflow-x-hidden px-6 pt-10 pb-24">
                <h3 class="text-xs font-semibold text-zinc-900 dark:text-white">On this page</h3>
                <nav class="mt-3">
                    <ul class="space-y-1">
                        @if (isset($page['headings']) && count($page['headings']) > 0)
                            @foreach($page['headings'] as $heading)
                                @include('lemme::partials.nav-link', [
                                    'href' => '#' . $heading['id'],
                                    'title' => $heading['text'],
                                    'active' => false,
                                    'showActiveIndicator' => true,
                                    'class' => $heading['class'] ?? ''
                                ])
                            @endforeach
                        @endif
                    </ul>
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

    <!-- modals -->
    @include('lemme::partials.search-modal')

    @livewireScripts
</body>
</html>
