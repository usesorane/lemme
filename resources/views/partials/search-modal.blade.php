<div x-show="searchModalOpen" x-cloak class="fixed inset-0 z-50" role="dialog" tabindex="-1" aria-modal="true">
    <!-- Backdrop/Overlay -->
    <div x-transition:enter="duration-300 ease-out" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="duration-200 ease-in" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-zinc-400/25 backdrop-blur-xs dark:bg-black/40" 
         aria-hidden="true">
    </div>
    
    <!-- Modal Container -->
    <div class="fixed inset-0 overflow-y-auto px-4 py-4 sm:px-6 sm:py-20 md:py-32 lg:px-8 lg:py-[15vh]">
        <div x-init="$watch('searchModalOpen', value => { if (value) { setTimeout(() => $refs.searchInput.focus(), 100) } })"
             x-transition:enter="duration-300 ease-out" 
             x-transition:enter-start="scale-95 opacity-0" 
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="duration-200 ease-in" 
             x-transition:leave-start="scale-100 opacity-100" 
             x-transition:leave-end="scale-95 opacity-0"
             @click.away="searchModalOpen = false"
             @click.stop
             @keydown.escape="searchModalOpen = false"
             class="mx-auto transform-gpu overflow-hidden rounded-lg bg-zinc-50 shadow-xl ring-1 ring-zinc-900/7.5 sm:max-w-xl dark:bg-zinc-900 dark:ring-zinc-800">
            <div role="combobox" aria-expanded="false" aria-haspopup="listbox">
                <form action="" novalidate="" role="search">
                    <div class="group relative flex h-12">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" class="pointer-events-none absolute top-0 left-3 h-full w-5 stroke-zinc-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12.01 12a4.25 4.25 0 1 0-6.02-6 4.25 4.25 0 0 0 6.02 6Zm0 0 3.24 3.25"></path>
                        </svg>
                        <input x-ref="searchInput" 
                               class="flex-auto appearance-none bg-transparent pl-10 text-zinc-900 outline-hidden placeholder:text-zinc-500 focus:w-full focus:flex-none sm:text-sm dark:text-white [&::-webkit-search-cancel-button]:hidden [&::-webkit-search-decoration]:hidden [&::-webkit-search-results-button]:hidden [&::-webkit-search-results-decoration]:hidden pr-4" 
                               aria-autocomplete="both" 
                               autocomplete="off" 
                               autocorrect="off" 
                               autocapitalize="none" 
                               enterkeyhint="search" 
                               spellcheck="false" 
                               placeholder="Find something..." 
                               maxlength="512" 
                               type="search" 
                               value="">
                    </div>
                    <div class="border-t border-zinc-200 bg-white empty:hidden dark:border-zinc-100/5 dark:bg-white/2.5"></div>
                </form>
            </div>
        </div>
    </div>
</div>