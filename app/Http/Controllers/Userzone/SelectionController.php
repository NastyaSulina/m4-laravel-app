<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Userzone\SelectionRequest;
use App\Models\Selection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SelectionController extends Controller
{
    public function destroy(Selection $selection): RedirectResponse
    {
        DB::transaction(function () use ($selection) {
            $selection->criteria()->delete();
            $selection->accommodations()->delete();
            $selection->delete();
        });

        return redirect()->route('userzone.selections.index');
    }

    public function index(Request $request): View
    {
        $selections = $request->user()->selections()->get();

        return view('userzone.selections.index', compact('selections'));
    }

    public function show(Selection $selection): View
    {
        $selection->load('accommodations.media', 'criteria');

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

    public function update(SelectionRequest $request, Selection $selection): RedirectResponse
    {
        $selection->title = $request->title;
        $selection->search_type = $request->search_type;
        $selection->start_date = $request->start_date;
        $selection->end_date = $request->end_date;
        $selection->save();

        return redirect()->route('userzone.selections.index');
    }

    public function store(SelectionRequest $request): RedirectResponse
    {
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
