<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Author;
use App\Models\Article;
use App\Models\Journal;

uses(RefreshDatabase::class);

test('author page is accessible', function () {
    $author = Author::factory()->create();
    
    $response = $this->get(route('authors.show', $author));

    $response->assertStatus(200);
    $response->assertSee($author->name);
});

test('author page lists their articles', function () {
    $author = Author::factory()->create();
    $journal = Journal::factory()->create();
    
    $article1 = Article::factory()->create([
        'journal_id' => $journal->id,
        'title' => 'First Article',
        'published_date' => now()->subDay(),
    ]);
    
    $article2 = Article::factory()->create([
        'journal_id' => $journal->id,
        'title' => 'Second Article',
        'published_date' => now(),
    ]);

    $author->articles()->attach([
        $article1->id => ['order' => 1],
        $article2->id => ['order' => 2]
    ]);

    $response = $this->get(route('authors.show', $author));

    $response->assertStatus(200);
    $response->assertSee('First Article');
    $response->assertSee('Second Article');
});

test('article page links to author page', function () {
    $author = Author::factory()->create(['name' => 'John Doe']);
    $journal = Journal::factory()->create();
    $article = Article::factory()->create(['journal_id' => $journal->id]);
    
    $author->articles()->attach($article->id, ['order' => 1]);

    $response = $this->get(route('articles.show', $article));

    $response->assertStatus(200);
    $response->assertSee(route('authors.show', $author));
    $response->assertSee('John Doe');
});
