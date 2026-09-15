<?php

namespace App\View\Components\App;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Page extends Component
{
    public function __construct(
        public string $title,
        public ?string $eyebrow = null,
        public bool $narrow = false,
        public bool $wide = false,
    ) {}

    public function render(): View
    {
        return view('components.app.page');
    }
}
