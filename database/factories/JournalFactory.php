<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Journal>
 */
class JournalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->company() . ' Journal',
            'oai_base_url' => fake()->url() . '/oai',
            'enabled' => true,
            'description' => fake()->paragraph(),
            'publisher_id' => \App\Models\Publisher::factory(),
            'website_url' => fake()->url(),
        ];
    }
}
