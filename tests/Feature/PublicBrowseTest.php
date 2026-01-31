<?php

use App\Models\Article;
use App\Models\Journal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('homepage returns successful response', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Academic Repository');
});

test('browse page lists articles', function () {
    $article = Article::factory()->create([
        'title' => 'Test Article',
    ]);

    $response = $this->get('/articles');
    $response->assertStatus(200);
    $response->assertSee('Test Article');
});

test('browse page can filter by journal', function () {
    $journal1 = Journal::factory()->create(['title' => 'Journal A']);
    $journal2 = Journal::factory()->create(['title' => 'Journal B']);
    
    Article::factory()->create([
        'journal_id' => $journal1->id,
        'title' => 'Article in Journal A',
    ]);
    
    Article::factory()->create([
        'journal_id' => $journal2->id,
        'title' => 'Article in Journal B',
    ]);

    $response = $this->get('/articles?journal=' . $journal1->id);
    $response->assertStatus(200);
    $response->assertSee('Article in Journal A');
    $response->assertDontSee('Article in Journal B');
});

test('search filters articles', function () {
    Article::factory()->create([
        'title' => 'Biology Research',
    ]);
    
    Article::factory()->create([
        'title' => 'Physics Study',
    ]);

    $response = $this->get('/articles?search=Biology');
    $response->assertStatus(200);
    $response->assertSee('Biology Research');
    $response->assertDontSee('Physics Study');
});

test('article detail page works', function () {
    $article = Article::factory()->create([
        'title' => 'Detailed Article',
        'abstract' => 'This is the abstract.',
    ]);

    $response = $this->get('/articles/' . $article->id);
    $response->assertStatus(200);
    $response->assertSee('Detailed Article');
    $response->assertSee('This is the abstract.');
});

test('journal profile page works', function () {
    $journal = Journal::factory()->create(['title' => 'My Great Journal']);
    Article::factory()->create([
        'journal_id' => $journal->id,
        'title' => 'Article in Profile',
    ]);

    $response = $this->get('/journals/' . $journal->id);
    $response->assertStatus(200);
    $response->assertSee('My Great Journal');
    $response->assertSee('Article in Profile');
});
