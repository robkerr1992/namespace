<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController
{
    public function store(Request $request, Bounty $bounty): JsonResponse
    {
        $validated = $request->validate([
            'submission' => 'required|string|min:1|max:255'
        ]);

        if ($request->user()->getKey() === $bounty->getAttribute('user_id')) {
            return response()->json(['error' => "You cannot submit to your own bounty."], 400);
        }

        if (
            $bounty->submissions()->whereRaw('LOWER(submission) = ?', [
                Str::of($validated['submission'])->lower()
            ])->exists()
        ) {
            return response()->json(['error' => "'${$validated['submission']}' has already been submitted!"], 400);
        }

        $bounty->submissions()->create([
            'user_id' => $request->user()->getKey(),
            'submission' => $validated['submission']
        ]);

        return response()->json(['message' => 'Success'], 200);
    }
}
