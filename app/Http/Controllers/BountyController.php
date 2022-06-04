<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BountyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Bounty/Index', [
            'active_bounties' => Bounty::active()->withCount('submissions')->get(),
            'completed_bounties' => Bounty::completed()->withCount('submissions')->get()
        ]);
    }

    public function show(Bounty $bounty): Response
    {
        $bounty->load('submissions');

        return Inertia::render('Bounty/Show', [
            'bounty' => $bounty,
            'submissions' => $bounty->submissions()->get(),
            'winning_submission' => $bounty->winningSubmission()->first()
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
            'description' => 'required|string',
        ]);

        $bounty = Bounty::create([
            'user_id' => $request->user()->getKey(),
            'mapping_key' => $validated['mapping_key'],
            'description' => $validated['description']
        ]);

        return redirect()->to('/bounty/'.$bounty->getKey());
    }
}
