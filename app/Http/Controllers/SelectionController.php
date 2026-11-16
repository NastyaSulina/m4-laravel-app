<?php

namespace App\Http\Controllers;

use App\Models\Selection;
use Illuminate\View\View;

class SelectionController extends Controller
{
    public function index(): View
    {
        $selections = Selection::all();

        return view('selections.index', compact('selections'));
    }
}
