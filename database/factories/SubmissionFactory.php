<?php

namespace Database\Factories;

use App\Models\Bounty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'bounty_id' => Bounty::factory(),
            'user_id' => User::factory(),
            'submission' => $this->faker->words(2, true),
            'won_at' => null
        ];
    }

    /**
     * Winning Submission
     *
     * @return static
     */
    public function won()
    {
        return $this->state(function (array $attributes) {
            return [
                'won_at' => now(),
            ];
        });
    }
}
