{{-- 
Parameters:
- $group: array - the group data with title, children, etc.
- $currentPageSlug: string - the slug of the current page
--}}

@php
$hasActiveChild = function($children, $currentSlug) use (&$hasActiveChild) {
    foreach ($children as $child) {
        if ($child['type'] === 'page' && $child['slug'] === $currentSlug) {
            return true;
        }
        if ($child['type'] === 'group' && $hasActiveChild($child['children'], $currentSlug)) {
            return true;
        }
    }
    return false;
};

$isExpanded = $hasActiveChild($group['children'], $currentPageSlug);
@endphp

<li>
    <!-- Group Header -->
    <div class="flex items-center justify-between py-2 px-4">
        <h3 class="text-xs font-semibold text-zinc-900 dark:text-white">
            {{ $group['title'] }}
        </h3>
    </div>
    
    <!-- Group Items -->
    <ul class="mt-3 space-y-1 ml-2">
        @foreach ($group['children'] as $child)
            @if ($child['type'] === 'page')
                @include('lemme::partials.nav-link', [
                    'href' => $child['url'],
                    'title' => $child['title'],
                    'active' => $currentPageSlug === $child['slug'],
                    'showActiveIndicator' => true
                ])
            @elseif ($child['type'] === 'group')
                @include('lemme::partials.nav-group', [
                    'group' => $child,
                    'currentPageSlug' => $currentPageSlug
                ])
            @endif
        @endforeach
    </ul>
</li>
