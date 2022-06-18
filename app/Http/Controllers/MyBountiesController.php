<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MyBountiesController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $active = $request->user()->bounties()->active()->withCount('submissions')->get();
        $completed = $request->user()->bounties()->completed()->withCount('submissions')->get();

        return Inertia::render('MyBounties', [
            'active' => $active->isEmpty() ? null : $active,
            'completed' => $completed->isEmpty() ? null : $completed
        ]);
    }
}
