<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Userzone\AccommodationCriteriaRequest;
use App\Models\Accommodation;
use App\Models\Selection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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
            $results[] = [
                'accommodation_id' => $accommodation->id,
                'criterion_id' => $criterion['id'],
                'is_met' => $criterion['is_met'],
            ];
        }

        DB::table('accommodation_criterion')->upsert(
            $results,
            ['accommodation_id', 'criterion_id'],
            ['is_met'],
        );

        return redirect()->route('userzone.selections.accommodations.show', [$selection, $accommodation]);
    }
}
