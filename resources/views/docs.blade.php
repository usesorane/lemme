<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page['title'] }} - {{ $siteTitle }}</title>
    <meta name="description" content="{{ $siteDescription }}">
    
    <!-- Tailwind CSS 4 -->
    <script src="https://cdn.tailwindcss.com/4.0.0-alpha.28/tailwindcss.js"></script>
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgb(156 163 175);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgb(107 114 128);
        }
        
        /* Content styles */
        .prose h1 { @apply text-3xl font-bold text-gray-900 mb-6 mt-8; }
        .prose h2 { @apply text-2xl font-semibold text-gray-900 mb-4 mt-6; }
        .prose h3 { @apply text-xl font-semibold text-gray-900 mb-3 mt-5; }
        .prose h4 { @apply text-lg font-medium text-gray-900 mb-2 mt-4; }
        .prose p { @apply text-gray-700 mb-4 leading-relaxed; }
        .prose ul { @apply list-disc pl-6 mb-4; }
        .prose ol { @apply list-decimal pl-6 mb-4; }
        .prose li { @apply mb-1; }
        .prose a { @apply text-blue-600 hover:text-blue-800 underline; }
        .prose code { @apply bg-gray-100 text-gray-800 px-1 py-0.5 rounded text-sm; }
        .prose pre { @apply bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto mb-4; }
        .prose pre code { @apply bg-transparent text-inherit px-0 py-0; }
        .prose blockquote { @apply border-l-4 border-blue-500 pl-4 italic text-gray-600 mb-4; }
        .prose table { @apply w-full border-collapse border border-gray-300 mb-4; }
        .prose th { @apply border border-gray-300 bg-gray-100 px-4 py-2 text-left font-semibold; }
        .prose td { @apply border border-gray-300 px-4 py-2; }
    </style>
</head>
<body class="h-full bg-gray-50 font-sans">
    <div x-data="{ sidebarOpen: false }" class="flex h-full">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
             class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">{{ $siteTitle }}</h1>
                <button @click="sidebarOpen = false" class="lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <nav class="flex-1 px-4 py-6 overflow-y-auto custom-scrollbar">
                <ul class="space-y-1">
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
        
        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 lg:hidden">
                <div class="flex items-center justify-between h-16 px-4">
                    <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900">{{ $page['title'] }}</h1>
                    <div></div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto custom-scrollbar">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <article class="prose max-w-none">
                        <x-markdown>
                            {!! $page['raw_content'] !!}
                        </x-markdown>
                    </article>
                    
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
            </main>
        </div>
    </div>
    
    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
         x-cloak
         @click="sidebarOpen = false"></div>
</body>
</html>
