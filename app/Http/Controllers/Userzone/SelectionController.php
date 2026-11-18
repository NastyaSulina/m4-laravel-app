<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;

use App\Models\Selection;
use Illuminate\Http\RedirectResponse;
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

    public function create(): View
    {
        return view('userzone.selections.create');
    }

    public function edit(Selection $selection): View
    {
        return view('userzone.selections.edit', compact('selection'));
    }

    public function update(Request $request, Selection $selection): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'search_type' => ['required', 'in:short_stay,long_term'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', ...($request->filled('start_date') ? ['after_or_equal:start_date'] : [])],
        ]);

        $selection->title = $request->title;
        $selection->search_type = $request->search_type;
        $selection->start_date = $request->start_date;
        $selection->end_date = $request->end_date;
        $selection->save();

        return redirect()->route('userzone.selections.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'search_type' => ['required', 'in:short_stay,long_term'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', ...($request->filled('start_date') ? ['after_or_equal:start_date'] : [])],
        ]);

        $selection = new Selection;
        $selection->title = $request->title;
        $selection->search_type = $request->search_type;
        $selection->status = 'active';
        $selection->start_date = $request->start_date;
        $selection->end_date = $request->end_date;
        $request->user()->selections()->save($selection);

        return redirect()->route('userzone.selections.index');
    }
}
