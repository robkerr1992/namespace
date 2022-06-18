<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public function store(Request $request, Bounty $bounty): RedirectResponse
    {
        $validated = $request->validate([
            'submission' => 'required|string|min:1|max:255'
        ]);

        if ($request->user()->getKey() === $bounty->getAttribute('user_id')) {
            return redirect()->back()->with('error', "You cannot submit to your own bounty.");
        }

        if (
            $bounty->submissions()->whereRaw('LOWER(submission) = ?', [
                Str::of($validated['submission'])->lower()
            ])->exists()
        ) {
            return redirect()->back()->with('error', "'{$validated['submission']}' has already been submitted!");
        }

        $bounty->submissions()->create([
            'user_id' => $request->user()->getKey(),
            'submission' => $validated['submission']
        ]);

        return redirect()->back()->with('success', 'Success');
    }
}
