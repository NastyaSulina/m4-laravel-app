<?php

namespace App\View\Components\Layout;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Header extends Component
{
    /** @var array<string, string> */
    public array $menu;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->menu = Auth::check()
            ? [
                'Selections' => route('userzone.selections.index'),
                'Profile' => route('profile.edit'),
            ]
            : ['Sign in' => route('login'), 'Sign up' => route('register')];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.layout.header');
    }
}
