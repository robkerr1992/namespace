<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use Inertia\Inertia;
use Inertia\Response;

class CompletedBountyController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Bounty/Completed', [
            'bounties' => Bounty::completed()->withCount('submissions')->get(),
        ]);
    }
}
