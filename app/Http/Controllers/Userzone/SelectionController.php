<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;

use App\Models\Selection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SelectionController extends Controller
{
    public function index(Request $request): View
    {
        $selections = $request->user()->selections()->get();

        return view('userzone.selections.index', compact('selections'));
    }

    public function show(Selection $selection): View
    {
        $selection->load('accommodations', 'criteria');

        return view('userzone.selections.show', compact('selection'));
    }
}
