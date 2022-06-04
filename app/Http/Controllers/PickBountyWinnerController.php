<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PickBountyWinnerController extends Controller
{
    public function __invoke(Request $request, Bounty $bounty, Submission $submission): JsonResponse
    {
        if ($request->user()->getKey() !== $bounty->getAttribute('user_id')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if ($bounty->winningSubmission()->exists()) {
            return response()->json(['error' => 'This bounty already has a winner.'], 400);
        }

        if ($submission->getAttribute('bounty_id') !== $bounty->getKey()) {
            return response()->json(['error' => 'This submission does not belong to this bounty.'], 400);
        }

        $submission->update(['won_at' => now()]);
        return response()->json(['message' => 'Success'], 200);
    }
}
