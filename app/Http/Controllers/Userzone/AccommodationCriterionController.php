<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Userzone\AccommodationCriteriaRequest;
use App\Models\Accommodation;
use App\Models\Selection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccommodationCriterionController extends Controller
{
    public function edit(Selection $selection, Accommodation $accommodation): View
    {
        $selection->load('criteria');
        $accommodation->load('criteria');

        return view('userzone.accommodations.criteria', compact('selection', 'accommodation'));
    }

    public function update(AccommodationCriteriaRequest $request, Selection $selection, Accommodation $accommodation): RedirectResponse
    {
        $results = [];

        foreach ($request->validated('criteria') as $criterion) {
            $results[$criterion['id']] = ['is_met' => $criterion['is_met']];
        }

        $accommodation->criteria()->syncWithoutDetachingOrFail($results);

        return redirect()->route('userzone.selections.accommodations.show', [$selection, $accommodation]);
    }
}
