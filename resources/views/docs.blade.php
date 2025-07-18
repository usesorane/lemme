<!DOCTYPE html>
<html lang="en" class="h-full">
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
<body class="flex min-h-full bg-white antialiased dark:bg-zinc-900">

    <div class="w-full">
        <div class="h-full lg:ml-72 xl:ml-80">
            <header class="contents lg:pointer-events-none lg:fixed lg:inset-0 lg:z-40 lg:flex">
                <div class="contents lg:pointer-events-auto lg:block lg:w-72 lg:overflow-y-auto lg:border-r lg:border-zinc-900/10 lg:px-6 lg:pt-4 lg:pb-8 xl:w-80 lg:dark:border-white/10">
                    <div class="hidden lg:flex">
                        LOGO
                    </div>
                    <div class="fixed inset-x-0 top-0 z-50 flex h-14 items-center justify-between gap-12 px-4 transition sm:px-6 lg:left-72 lg:z-30 lg:px-8 xl:left-80 backdrop-blur-xs lg:left-72 xl:left-80 dark:backdrop-blur-sm bg-white/(--bg-opacity-light) dark:bg-zinc-900/(--bg-opacity-dark)">
                        TOP NAVIGATION
                    </div>
                    <nav class="hidden lg:mt-10 lg:block">
                        <ul>
                            @foreach($navigation as $item)
                            <li>
                                <a href="{{ $item['url'] }}" 
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 
                                        {{ $page['slug'] === $item['slug'] ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                    {{ $item['title'] }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </header>
            <div class="relative flex h-full flex-col px-4 pt-14 sm:px-6 lg:px-8">
                <main class="flex flex-auto">
                    <article class="flex h-full flex-col pt-16 pb-10 flex-1">
                        <x-markdown class="flex-auto max-w-prose mx-auto prose dark:prose-invert">
                            {!! $page['raw_content'] !!}
                        </x-markdown>
                    </article>
                    
                    <!-- On This Page Sidebar -->
                    <aside class="hidden xl:block xl:w-64 xl:flex-shrink-0 xl:pl-8">
                        <div class="sticky top-28 pt-16">
                            <div class="space-y-3">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">On this page</h3>
                                <nav id="table-of-contents" class="space-y-1">
                                    <!-- TOC will be populated by JavaScript -->
                                </nav>
                            </div>
                        </div>
                    </aside>
                </main>
                <footer class="mx-auto w-full max-w-2xl space-y-10 pb-16 lg:max-w-5xl">
                    <div class="flex">
                        <div class="ml-auto flex flex-col items-end gap-3">
                            <!-- Page navigation -->
                            @if($pages->count() > 1)
                                <nav class="flex items-center justify-between border-t border-gray-200 mt-12 pt-6">
                                    @php
                                        $currentIndex = $pages->search(fn($p) => $p['slug'] === $page['slug']);
                                        $prevPage = $currentIndex > 0 ? $pages[$currentIndex - 1] : null;
                                        $nextPage = $currentIndex < $pages->count() - 1 ? $pages[$currentIndex + 1] : null;
                                    @endphp
                                    
                                    <div class="flex-1">
                                        @if($prevPage)
                                            <a href="{{ Sorane\Lemme\Facades\Lemme::getPageUrl($prevPage['slug']) }}" 
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                                {{ $prevPage['title'] }}
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 text-right">
                                        @if($nextPage)
                                            <a href="{{ Sorane\Lemme\Facades\Lemme::getPageUrl($nextPage['slug']) }}" 
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                {{ $nextPage['title'] }}
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </nav>
                            @endif
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tocContainer = document.getElementById('table-of-contents');
            const headings = document.querySelectorAll('.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6');
            
            if (headings.length === 0) {
                // Hide the entire "On this page" section if no headings
                const tocSection = tocContainer.closest('aside');
                if (tocSection) tocSection.style.display = 'none';
                return;
            }
            
            headings.forEach((heading, index) => {
                // Create an ID for the heading if it doesn't have one
                if (!heading.id) {
                    heading.id = 'heading-' + index;
                }
                
                const link = document.createElement('a');
                link.href = '#' + heading.id;
                link.textContent = heading.textContent;
                link.className = `block py-1 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-150 ${getHeadingClass(heading.tagName)}`;
                
                tocContainer.appendChild(link);
            });
            
            // Smooth scrolling for anchor links
            tocContainer.addEventListener('click', function(e) {
                if (e.target.tagName === 'A') {
                    e.preventDefault();
                    const targetId = e.target.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
        
        function getHeadingClass(tagName) {
            switch(tagName.toLowerCase()) {
                case 'h1': return 'font-medium';
                case 'h2': return 'font-medium';
                case 'h3': return 'pl-2';
                case 'h4': return 'pl-4';
                case 'h5': return 'pl-6';
                case 'h6': return 'pl-8';
                default: return '';
            }
        }
    </script>
</body>
</html>
