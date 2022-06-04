<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class TestController
{
    public function index(): Response
    {
        return Inertia::render('Test');
    }
}
