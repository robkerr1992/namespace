<?php

namespace App\Http\Controllers;

use App\Models\Bounty;
use Inertia\Inertia;
use Inertia\Response;

class ActiveBountyController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Bounty/Active', [
            'bounties' => Bounty::active()->where('deadline', '>', \Carbon\Carbon::now()->timestamp)->orderBy('deadline', 'asc')->withCount('submissions')->get(),
        ]);
    }
}
