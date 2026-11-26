<?php

namespace App\View\Components\Layout;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    /** @var array<string, array<string, string>> */
    public array $menu;

    public function __construct()
    {
        $this->menu = [
            'Legal' => ['Privacy Policy' => route('privacy-policy')],
            'Support' => ['Contacts' => route('contacts')],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.layout.footer');
    }
}
