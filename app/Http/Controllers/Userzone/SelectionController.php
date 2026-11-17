<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;

use App\Models\Selection;
use Illuminate\View\View;

class SelectionController extends Controller
{
    public function index(): View
    {
        $selections = Selection::all();

        return view('userzone.selections.index', compact('selections'));
    }

    public function show(Selection $selection): View
    {
        $selection->load('accommodations', 'criteria');

        return view('userzone.selections.show', compact('selection'));
    }
}
