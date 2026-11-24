<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SelectionUserController extends Controller
{
    public function index(Selection $selection): View
    {
        $users = $selection->users()->get();

        return view('userzone.users.index', compact('selection', 'users'));
    }

    public function store(Request $request, Selection $selection): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'exists:users,email'],
        ], [
            'email.exists' => 'No user with this email was found.',
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if ($user->id === $selection->owner_id) {
            throw ValidationException::withMessages([
                'email' => 'The owner already has access to this selection.',
            ]);
        }

        $selection->users()->syncWithoutDetaching([$user->id]);

        return redirect()->route('userzone.selections.users.index', $selection);
    }

    public function destroy(Selection $selection, User $user): RedirectResponse
    {
        $selection->users()->detach($user->id);

        return redirect()->route('userzone.selections.users.index', $selection);
    }
}
