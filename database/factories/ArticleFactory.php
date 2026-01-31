<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_id' => Journal::factory(),
            'issue_id' => Issue::factory(), // We'll need Issue factory too, or I can use closure
            'title' => fake()->sentence(),
            'slug' => fake()->slug(),
            'abstract' => fake()->paragraph(),
            'keywords' => implode(', ', fake()->words(3)),
            'source_url' => fake()->url(),
            'pdf_url' => fake()->url() . '.pdf',
            'published_date' => fake()->date(),
            'doi' => '10.' . fake()->numberBetween(1000, 9999) . '/' . fake()->slug(),
            'oai_id' => 'oai:' . fake()->domainName() . ':' . fake()->uuid(),
            'pages' => fake()->numberBetween(1, 100) . '-' . fake()->numberBetween(101, 200),
        ];
    }
}
