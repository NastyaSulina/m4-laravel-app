<?php

namespace App\Http\Controllers;

use Illuminate\Support\HtmlString;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        $content = cache()->flexible('contacts:content', [3600, 7200], function (): string {
            return view('contacts.content')->render();
        });

        return view('contacts.index', ['content' => new HtmlString($content)]);
    }
}
