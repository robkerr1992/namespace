<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $activeSubmissions = $request->user()->submissions()->active()->with('bounty')->get();
        $wonBounties = Bounty::whereHas('winningSubmission', function ($query) use ($request) {
            return $query->where('user_id', $request->user()->getKey());
        })->withCount('submissions')->get();

        return Inertia::render('Dashboard', [
            'activeSubmissions' => $activeSubmissions->isEmpty() ? null : $activeSubmissions,
            'wonBounties' => $wonBounties->isEmpty() ? null : $wonBounties
        ]);
    }
}
