<li>
    <div class="flex items-center justify-between py-2 px-4">
        <h3 class="text-xs font-semibold text-zinc-900 dark:text-white">
            {{ $group['title'] }}
        </h3>
    </div>
    <ul class="mt-3 ml-4 border-l border-l-lemme-accent/20 space-y-1">
        @foreach ($group['children'] as $child)
            @if ($child['type'] === 'page')
                <x-lemme::nav-link :href="$child['url']" :title="$child['title']" :active="$currentPageSlug === $child['slug']" />
            @elseif ($child['type'] === 'group')
                <x-lemme::nav-group :group="$child" :current-page-slug="$currentPageSlug" />
            @endif
        @endforeach
    </ul>
</li>
