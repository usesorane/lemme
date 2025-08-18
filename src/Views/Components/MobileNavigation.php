<?php

namespace Sorane\Lemme\Views\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MobileNavigation extends Component
{
    public function __construct() {}

    public function render(): View
    {
        return view('lemme::components.mobile-navigation');
    }
}
