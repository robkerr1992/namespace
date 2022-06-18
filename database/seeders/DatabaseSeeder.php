<?php

namespace Database\Seeders;

use App\Models\Bounty;
use App\Models\Submission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Bounty::factory()
            ->has(Submission::factory()->count(25))
            ->has(Submission::factory()->won()->count(1))
            ->count(20)
            ->create();

        Bounty::factory()
            ->has(Submission::factory()->count(11))
            ->count(5)
            ->create();

        Bounty::factory([
            'deadline' => \Carbon\Carbon::now()->subDay(2)->timestamp
        ])
            ->has(Submission::factory()->count(35))
            ->count(5)
            ->create();
    }
}
