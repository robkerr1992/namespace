<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BountyController extends Controller
{
    public function show(Bounty $bounty): Response
    {
        $bounty->load('submissions', 'poster');
        $submissions = $bounty->submissions()->with('submitter')->orderBy('won_at', 'desc')->get();
        $winningSubmission = $bounty->winningSubmission()->with('submitter')->first();

        return Inertia::render('Bounty/Show', [
            'bounty' => $bounty,
            'submissions' => $submissions->isEmpty() ? null : $submissions,
            'winningSubmission' => $winningSubmission
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Bounty/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mapping_key' => 'required|string',
            'value' => 'required|string',
            'description' => 'required|string',
            'deadline' => 'required|string',
        ]);

        $bounty = Bounty::create([
            'user_id' => $request->user()->getKey(),
            'mapping_key' => $validated['mapping_key'],
            'description' => $validated['description'],
            'value' => $validated['value'],
            'deadline' => $validated['deadline'],
        ]);

        return redirect()->to('/bounty/'.$bounty->getKey())->with('success', 'Bounty Created Successfully!');
    }
}
