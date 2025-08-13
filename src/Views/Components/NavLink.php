<?php

namespace Sorane\Lemme\Views\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class NavLink extends Component
{
    public function __construct(
        public string $href,
        public string $title,
        public bool $active = false,
        public bool $showActiveIndicator = true,
        public string $activeClasses = 'bg-zinc-800/2.5 dark:bg-white/2.5',
        public string $inactiveClasses = 'hover:bg-zinc-800/2.5 dark:hover:bg-white/2.5',
    ) {}

    public function render(): View
    {
        return view('lemme::components.nav-link');
    }
}
