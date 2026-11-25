<?php

namespace App\Http\Controllers;

use Illuminate\Support\HtmlString;
use Illuminate\View\View;

class PrivacyPolicyController extends Controller
{
    public function __invoke(): View
    {
        $content = cache()->flexible('privacy-policy:content', [3600, 7200], function (): string {
            return view('privacy-policy.content')->render();
        });

        return view('privacy-policy.index', ['content' => new HtmlString($content)]);
    }
}
