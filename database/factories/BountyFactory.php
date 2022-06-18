<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bounty>
 */
class BountyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'mapping_key' => $this->faker->word,
            'description' => $this->faker->words(10, true),
            'value' => $this->faker->randomNumber(9) . $this->faker->randomNumber(9),
            'deadline' => \Carbon\Carbon::now()->addWeeks(2)->timestamp,
            'user_id' => User::factory()
        ];
    }
}
