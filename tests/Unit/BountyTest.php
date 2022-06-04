<?php

namespace Tests\Unit;

use App\Models\WinningSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GenerateKeyPair;
use Tests\TestCase;

class BountyTest extends TestCase
{
    use GenerateKeyPair;
    use RefreshDatabase;

    public function test_it_can_have_winning_submissions(): void
    {
        $winningSubmission = WinningSubmission::factory()->create();
        $bounty = $winningSubmission->bounty;

        $this->assertEquals($bounty->winningSubmission->submission, $winningSubmission->submission);
    }
}
