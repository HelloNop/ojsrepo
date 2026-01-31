<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Author;
use App\Models\Issue;
use App\Models\Journal;
use App\Services\OaiPmhService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class HarvestJournalJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $timeout = 600; // 10 minutes timeout for the job

    /**
     * Create a new job instance.
     */
    public function __construct(public Journal $journal)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(OaiPmhService $harvester): void
    {
        // Check if harvest can start (prevent concurrent harvests)
        if (! $this->journal->canStartHarvest()) {
            Log::warning("Harvest already running for journal {$this->journal->id}. Skipping.");

            return;
        }

        // Create harvest log entry
        $harvestLog = $this->journal->harvestLogs()->create([
            'status' => 'running',
            'started_at' => now(),
        ]);

        // Mark journal as harvesting
        $this->journal->startHarvest();

        Log::info("Starting harvest for journal: {$this->journal->title} ({$this->journal->id})");

        $count = 0;
        $failedCount = 0;
        $checkpointInterval = 50; // Save progress every 50 records

        try {
            // Determine 'from' date for incremental harvesting
            $from = $this->journal->last_harvested_at;
            if ($from) {
                Log::info('Harvesting from: '.$from->toIso8601String());
            }

            $records = $harvester->listRecords(
                $this->journal->oai_base_url,
                $this->journal->set_spec,
                $from
            );

            foreach ($records as $data) {
                try {
                    $count++;

                    // 1. Handle Issue
                    $issue = $this->resolveIssue($data);

                    // 2. Handle Article
                    $article = Article::updateOrCreate(
                        ['oai_id' => $data['oai_id']],
                        [
                            'journal_id' => $this->journal->id,
                            'issue_id' => $issue->id,
                            'title' => $data['title'],
                            'abstract' => $data['abstract'],
                            'keywords' => $data['keywords'],
                            'source_url' => $data['url'] ?? '',
                            'pdf_url' => $data['pdf_url'] ?? '',
                            'published_date' => $data['published_date'],
                            'doi' => $data['doi'] ?? '',
                            'pages' => $data['pages'],
                            'slug' => \Illuminate\Support\Str::limit(\Illuminate\Support\Str::slug($data['title']), 250, ''),
                        ]
                    );

                    // 3. Handle Authors
                    $syncData = [];
                    foreach ($data['authors'] as $index => $authorName) {
                        $author = Author::firstOrCreate(
                            ['name' => $authorName],
                            ['affiliation' => '']
                        );
                        $syncData[$author->id] = ['order' => $index + 1];
                    }
                    $article->authors()->sync($syncData);

                    // Save checkpoint every N records to avoid re-processing on timeout
                    if ($count % $checkpointInterval === 0) {
                        $this->journal->update(['last_harvested_at' => now()]);
                        $harvestLog->updateProgress($count, $failedCount);
                        Log::info("Checkpoint: Processed $count records for journal {$this->journal->id}");
                    }
                } catch (\Exception $recordError) {
                    $failedCount++;
                    // Log individual record errors but continue processing
                    Log::warning("Failed to process record {$data['oai_id']}: ".$recordError->getMessage());

                    continue;
                }
            }

            Log::info("Successfully harvested $count records for journal {$this->journal->id}");

            // Mark harvest as successful
            $harvestLog->markAsSuccess($count, $failedCount);

            // Final update of last harvested timestamp and clear harvest_started_at
            $this->journal->finishHarvest();

        } catch (\Exception $e) {
            // Log the error
            $errorMessage = $e->getMessage();
            Log::error("Harvest error for journal {$this->journal->id}: ".$errorMessage);

            // Mark harvest log as failed or partial
            $harvestLog->markAsFailed($errorMessage, $count, $failedCount);

            // Clear harvest_started_at but keep last_harvested_at if we processed some records
            if ($count > 0) {
                Log::warning("Harvest partially completed for journal {$this->journal->id}. Processed $count records before error.");
                $this->journal->update([
                    'harvest_started_at' => null,
                    'last_harvested_at' => now(),
                ]);
            } else {
                $this->journal->update(['harvest_started_at' => null]);
                $this->fail($e);
            }
        }
    }

    protected function resolveIssue(array $data): Issue
    {
        $year = $data['year'] ?? now()->year;
        $title = $data['issue_title'] ?? ('Year '.$year);

        return Issue::firstOrCreate(
            [
                'journal_id' => $this->journal->id,
                'title' => $title,
            ],
            [
                'year' => $year,
            ]
        );
    }
}
