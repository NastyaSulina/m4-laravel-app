<?php

namespace App\View\Components;

use Illuminate\View\View;
use Illuminate\View\Component;

class BaseLayout extends Component
{
    public function __construct(public string $title = 'Default title')
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('layouts.base');
    }
}
