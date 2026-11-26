<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Userzone\AccommodationRequest;
use App\Models\Accommodation;
use App\Models\Selection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function create(Selection $selection): View
    {
        return view('userzone.accommodations.create', compact('selection'));
    }

    public function store(AccommodationRequest $request, Selection $selection): RedirectResponse
    {
        $accommodation = $selection->accommodations()->create([
            ...Arr::except($request->validated(), ['image']),
            'user_id' => $request->user()->id,
        ]);

        if ($request->hasFile('image')) {
            $accommodation->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return redirect()->route('userzone.selections.show', $selection);
    }

    public function show(Selection $selection, Accommodation $accommodation): View
    {
        return view('userzone.accommodations.show', compact('selection', 'accommodation'));
    }

    public function edit(Selection $selection, Accommodation $accommodation): View
    {
        return view('userzone.accommodations.edit', compact('selection', 'accommodation'));
    }

    public function update(AccommodationRequest $request, Selection $selection, Accommodation $accommodation): RedirectResponse
    {
        $accommodation->update([
            'note' => null,
            ...Arr::except($request->validated(), ['image']),
        ]);

        if ($request->hasFile('image')) {
            $accommodation->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return redirect()->route('userzone.selections.accommodations.show', [$selection, $accommodation]);
    }

    public function destroy(Request $request, Selection $selection, Accommodation $accommodation): RedirectResponse
    {
        $isSelectionOwner = $selection->owner_id === $request->user()->id;
        $isAccommodationAuthor = $accommodation->user_id === $request->user()->id;

        abort_unless($isSelectionOwner || $isAccommodationAuthor, 403);

        $accommodation->delete();

        return redirect()->route('userzone.selections.show', $selection);
    }
}
