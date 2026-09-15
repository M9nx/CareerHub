<?php

namespace App\View\Components\App;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShellLayout extends Component
{
    public function render(): View
    {
        return view('components.app.shell-layout');
    }
}
