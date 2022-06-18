<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeclareWinnerController extends Controller
{
    public function __invoke(Request $request, Bounty $bounty, Submission $submission): RedirectResponse
    {
        if ($request->user()->getKey() !== $bounty->getAttribute('user_id')) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        if ($bounty->winningSubmission()->exists()) {
            return redirect()->back()->with('error', 'This bounty already has a winner.');
        }

        if ($submission->getAttribute('bounty_id') !== $bounty->getKey()) {
            return  redirect()->back()->with('error', 'This submission does not belong to this bounty.');
        }

        if (!$bounty->deadlineHasPassed()) {
            return redirect()->back()->with('error', 'This bounty is still accepting submissions!');
        }

        $submission->update(['won_at' => now()]);

        return redirect()->back()->with('success', "Success! $submission->submission is the winner!");
    }
}
