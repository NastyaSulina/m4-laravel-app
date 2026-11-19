<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Userzone\CriterionRequest;
use App\Models\Criterion;
use App\Models\Selection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CriterionController extends Controller
{
    public function index(Selection $selection): View
    {
        $criteria = $selection->criteria()->get();

        return view('userzone.criteria.index', compact('selection', 'criteria'));
    }

    public function store(CriterionRequest $request, Selection $selection): RedirectResponse
    {
        $selection->criteria()->create([
            ...$request->validated(),
            'is_required' => $request->boolean('is_required'),
        ]);

        return redirect()->route('userzone.selections.criteria.index', $selection);
    }

    public function edit(Selection $selection, Criterion $criterion): View
    {
        return view('userzone.criteria.edit', compact('selection', 'criterion'));
    }

    public function update(CriterionRequest $request, Selection $selection, Criterion $criterion): RedirectResponse
    {
        $criterion->update([
            ...$request->validated(),
            'is_required' => $request->boolean('is_required'),
        ]);

        return redirect()->route('userzone.selections.criteria.index', $selection);
    }

    public function destroy(Selection $selection, Criterion $criterion): RedirectResponse
    {
        $criterion->delete();

        return redirect()->route('userzone.selections.criteria.index', $selection);
    }
}
