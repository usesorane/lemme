<div x-init="$watch('searchModalOpen', value => { if (value) { setTimeout(() => $refs.searchInput.focus(), 100) } })">
    <div class="group relative flex h-12">
        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" class="pointer-events-none absolute top-0 left-3 h-full w-5 stroke-zinc-500">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12.01 12a4.25 4.25 0 1 0-6.02-6 4.25 4.25 0 0 0 6.02 6Zm0 0 3.24 3.25"></path>
        </svg>
        <input 
            wire:model.live.debounce.300ms="search"
            x-ref="searchInput" 
            class="flex-auto appearance-none bg-transparent pl-10 text-zinc-900 outline-hidden placeholder:text-zinc-500 focus:w-full focus:flex-none sm:text-sm dark:text-white [&::-webkit-search-cancel-button]:hidden [&::-webkit-search-decoration]:hidden [&::-webkit-search-results-button]:hidden [&::-webkit-search-results-decoration]:hidden pr-4" 
            aria-autocomplete="both" 
            autocomplete="off" 
            autocorrect="off" 
            autocapitalize="none" 
            enterkeyhint="search" 
            spellcheck="false" 
            placeholder="Find something..." 
            maxlength="512" 
            type="search">
    </div>
    
    <div class="border-t border-zinc-200 bg-white dark:border-zinc-100/5 dark:bg-white/2.5">
        @if (count($results) > 0)
            <ul role="listbox" class="max-h-80 overflow-y-auto">
                @foreach($results as $index => $result)
                    <li class="group block cursor-pointer px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 {{ $index > 0 ? 'border-t border-zinc-100 dark:border-zinc-800' : '' }}" 
                        wire:click="$dispatch('navigate-to', { url: '{{ $result['url'] }}' })">
                        <div class="text-sm font-medium text-zinc-900 group-hover:text-emerald-500 dark:text-white">
                            @if(strlen($search) > 0)
                                {!! $this->highlightSearchTerm($result['title'], $search) !!}
                            @else
                                {{ $result['title'] }}
                            @endif
                        </div>
                        <div class="mt-1 truncate text-2xs whitespace-nowrap text-zinc-500">
                            <span>{{ $result['category'] }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @elseif (strlen(trim($search)) > 0)
            <div class="px-4 py-8 text-center text-sm text-zinc-500">
                No results found for "{{ $search }}"
            </div>
        @endif
    </div>
</div>
