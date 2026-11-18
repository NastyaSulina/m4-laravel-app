<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\Selection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CriterionController extends Controller
{
    public function edit(Selection $selection, Criterion $criterion): View
    {
        return view('userzone.criteria.edit', compact('selection', 'criterion'));
    }

    public function update(Request $request, Selection $selection, Criterion $criterion): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_required' => ['boolean'],
        ]);

        $criterion->title = $request->title;
        $criterion->is_required = $request->boolean('is_required');
        $criterion->save();

        return redirect()->route('userzone.selections.show', $selection);
    }

    public function destroy(Selection $selection, Criterion $criterion): RedirectResponse
    {
        $criterion->delete();

        return redirect()->route('userzone.selections.show', $criterion->selection_id);
    }
}
