<?php

use App\Jobs\HarvestJournalJob;
use App\Models\Article;
use App\Models\Author;
use App\Models\Issue;
use App\Models\Journal;
use App\Services\OaiPmhService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('job harvests and stores records correctly', function () {
    // 1. Setup Journal
    $journal = Journal::factory()->create([
        'title' => 'Test Journal',
        'oai_base_url' => 'http://example.com/oai',
        'enabled' => true,
    ]);

    // 2. Mock Service
    $mockData = [
        [
            'oai_id' => 'oai:test:1',
            'title' => 'Article Title',
            'authors' => ['Doe, John', 'Smith, Jane'],
            'abstract' => 'Abstract content',
            'keywords' => 'key1, key2',
            'published_date' => '2024-01-01',
            'url' => 'http://example.com/1',
            'pdf_url' => 'http://example.com/file.pdf',
            'doi' => '10.1234/5678',
            'publisher' => 'Publisher',
            'year' => '2024',
            'pages' => '1-10',
            'issue_title' => 'Vol. 1 No. 1',
            'journal_title' => 'Test Journal',
        ]
    ];

    $mockService = mock(OaiPmhService::class, function (MockInterface $mock) use ($mockData) {
        $mock->shouldReceive('listRecords')
            ->once()
            ->andReturn((function () use ($mockData) {
                foreach ($mockData as $data) {
                    yield $data;
                }
            })());
    });

    // 3. Dispatch Job (synchronously)
    (new HarvestJournalJob($journal))->handle($mockService);

    // 4. Assert Database
    expect(Issue::count())->toBe(1);
    $issue = Issue::first();
    expect($issue->title)->toBe('Vol. 1 No. 1');
    expect($issue->year)->toBe('2024');

    expect(Article::count())->toBe(1);
    $article = Article::first();
    expect($article->title)->toBe('Article Title');
    expect($article->oai_id)->toBe('oai:test:1');
    expect($article->pages)->toBe('1-10');
    expect($article->pdf_url)->toBe('http://example.com/file.pdf');

    expect(Author::count())->toBe(2);
    expect($article->authors)->toHaveCount(2);
    expect($article->authors->pluck('name'))->toContain('Doe, John');
    // Note: The parsing logic in Service does formatName, but here we mocked the return of listRecords.
    // So if the mock returns 'Doe, John', the Job just saves 'Doe, John'. 
    // Wait, the Job relies on the Service to have ALREADY formatted the authors if it uses parseRecord?
    // Let's check OaiPmhService::parseRecord. Yes, it calls formatName.
    // So my mock data above should simulate what parseRecord returns.
    // If parseRecord returns "John Doe", then the Job receives "John Doe".
    // I put "Doe, John" in the mock, so the Job will save "Doe, John". 
    // This confirms the Job just takes what the Service gives.
    
    $journal->refresh();
    expect($journal->last_harvested_at)->not->toBeNull();
});
