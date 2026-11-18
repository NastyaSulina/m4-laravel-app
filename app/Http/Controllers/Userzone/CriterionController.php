<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\Selection;
use Illuminate\Http\RedirectResponse;

class CriterionController extends Controller
{
    public function destroy(Selection $selection, Criterion $criterion): RedirectResponse
    {
        $criterion->delete();

        return redirect()->route('userzone.selections.show', $criterion->selection_id);
    }
}
