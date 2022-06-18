<?php

use App\Models\Bounty;
use App\Models\Submission;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'stats' => [
            ['id' => 0, 'name' => 'Active Bounties', 'stat' => Bounty::active()->count()],
            ['id' => 1, 'name' => 'Completed Bounties', 'stat' => Bounty::completed()->count()],
            ['id' => 2, 'name' => 'Submissions', 'stat' => Submission::count()]
        ]
    ]);
});

Route::get('/test', [\App\Http\Controllers\TestController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, '__invoke'])->name('dashboard');
    Route::post('/bounty', [\App\Http\Controllers\BountyController::class, 'store'])->name('bounty.store');
    Route::get('/bounty/create', [\App\Http\Controllers\BountyController::class, 'create'])->name('bounty.create');
    Route::get('/bounty/active', [\App\Http\Controllers\ActiveBountyController::class, '__invoke'])->name('bounty.active');
    Route::get('/bounty/completed', [\App\Http\Controllers\CompletedBountyController::class, '__invoke'])->name('bounty.completed');
    Route::get('/bounty/{bounty}', [\App\Http\Controllers\BountyController::class, 'show'])->name('bounty.show');
    Route::post('/submission/{bounty}', [\App\Http\Controllers\SubmissionController::class, 'store'])->name('submission.store');
    Route::post('/declare-winner/{bounty}/{submission}', [\App\Http\Controllers\DeclareWinnerController::class, '__invoke'])->name('declare-winner');
    Route::get('/user/edit', [\App\Http\Controllers\Auth\UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/edit', [\App\Http\Controllers\Auth\UserController::class, 'update'])->name('user.update');
    Route::get('/my-bounties', [\App\Http\Controllers\MyBountiesController::class, '__invoke'])->name('my-bounties');
});

require __DIR__.'/auth.php';
