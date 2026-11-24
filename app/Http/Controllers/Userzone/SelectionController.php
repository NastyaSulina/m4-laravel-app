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
    public function index(Request $request): View
    {
        $filter = $request->query('filter') === 'shared' ? 'shared' : 'my';
        $user = $request->user();

        $query = $filter === 'shared'
            ? $user->sharedSelections()->where('owner_id', '!=', $user->id)->with('owner')
            : $user->selections();

        $selections = $query->get();

        return view('userzone.selections.index', compact('selections', 'filter'));
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

    public function store(SelectionRequest $request): RedirectResponse
    {
        $request->user()->selections()->create($request->validated());

        return redirect()->route('userzone.selections.index');
    }

    public function edit(Selection $selection): View
    {
        return view('userzone.selections.edit', compact('selection'));
    }

    public function update(SelectionRequest $request, Selection $selection): RedirectResponse
    {
        $selection->update([
            'start_date' => null,
            'end_date' => null,
            ...$request->validated(),
        ]);

        return redirect()->route('userzone.selections.show', $selection);
    }

    public function destroy(Selection $selection): RedirectResponse
    {
        DB::transaction(function () use ($selection) {
            $selection->criteria()->delete();
            foreach ($selection->accommodations()->get() as $accommodation) {
                $accommodation->delete();
            }
            $selection->delete();
        });

        return redirect()->route('userzone.selections.index');
    }
}
