<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClaimBountyController extends Controller
{
    public function __invoke(Request $request, Bounty $bounty): RedirectResponse
    {
        if ($bounty->claimed()) {
            return redirect()->back()->with('error', 'Bounty already claimed.');
        }

        if ($bounty->winningSubmission()->doesntExist()) {
            return redirect()->back()->with('error', 'This bounty hasn\'t been completed.');
        }

        if ($bounty->winningSubmission()->where('user_id', $request->user()->getKey())->doesntExist()) {
            return redirect()->back()->with('error', 'Only the winner can claim this bounty.');
        }

        $bounty->update(['claimed' => true]);

        return redirect()->route('bounty.show', $bounty->getKey())->with('success', 'Bounty claimed! The funds are being transferred to the winning address.');
    }
}
