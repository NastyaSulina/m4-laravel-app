<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Userzone\AccommodationRequest;
use App\Models\Accommodation;
use App\Models\Selection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function create(Selection $selection): View
    {
        return view('userzone.accommodations.create', compact('selection'));
    }

    public function store(AccommodationRequest $request, Selection $selection): RedirectResponse
    {
        $accommodation = new Accommodation;
        $accommodation->user_id = $request->user()->id;
        $accommodation->title = $request->title;
        $accommodation->source_url = $request->source_url;
        $accommodation->address = $request->address;
        $accommodation->status = $request->status;
        $accommodation->property_type = $request->property_type;
        $accommodation->note = $request->note;

        if ($request->hasFile('image')) {
            $accommodation->addMediaFromRequest('image')->toMediaCollection('images');
        }

        $selection->accommodations()->save($accommodation);

        return redirect()->route('userzone.selections.show', $selection);
    }

    public function show(Selection $selection, Accommodation $accommodation): View
    {
        return view('userzone.accommodations.show', compact('accommodation'));
    }

    public function edit(Selection $selection, Accommodation $accommodation): View
    {
        return view('userzone.accommodations.edit', compact('selection', 'accommodation'));
    }

    public function update(AccommodationRequest $request, Selection $selection, Accommodation $accommodation): RedirectResponse
    {
        $accommodation->title = $request->title;
        $accommodation->source_url = $request->source_url;
        $accommodation->address = $request->address;
        $accommodation->status = $request->status;
        $accommodation->property_type = $request->property_type;
        $accommodation->note = $request->note;
        $accommodation->save();

        if ($request->hasFile('image')) {
            $accommodation->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return redirect()->route('userzone.selections.accommodations.show', [$selection, $accommodation]);
    }
}
