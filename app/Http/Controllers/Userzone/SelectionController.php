<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Userzone\SelectionRequest;
use App\Models\Selection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function show(Request $request, Selection $selection): View
    {
        $selection->load('criteria');
        $selectedCriteria = $selection->criteria
            ->whereIn('id', (array) $request->query('criteria', []))
            ->modelKeys();
        $query = $selection->accommodations()->with('media', 'criteria');

        foreach ($selectedCriteria as $criterionId) {
            $query->whereHas('criteria', function (Builder $query) use ($criterionId): void {
                $query->where('criteria.id', $criterionId)->where('accommodation_criterion.is_met', true);
            });
        }

        $accommodations = $query->get();

        return view('userzone.selections.show', compact('selection', 'accommodations', 'selectedCriteria'));
    }

    public function create(): View
    {
        return view('userzone.selections.create');
    }

    public function store(SelectionRequest $request): RedirectResponse
    {
        $selection = $request->user()->selections()->create($request->validated());

        return redirect()->route('userzone.selections.criteria.index', $selection);
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
        $selection->delete();

        return redirect()->route('userzone.selections.index');
    }
}
