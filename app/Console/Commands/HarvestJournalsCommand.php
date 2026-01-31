<?php

namespace App\Console\Commands;

use App\Jobs\HarvestJournalJob;
use App\Models\Journal;
use Illuminate\Console\Command;

class HarvestJournalsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:harvest-journals {--journal= : The ID of the journal to harvest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harvest articles from OAI-PMH for enabled journals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $journalId = $this->option('journal');

        $query = Journal::query()->where('enabled', true);

        if ($journalId) {
            $query->where('id', $journalId);
        }

        $journals = $query->get();

        if ($journals->isEmpty()) {
            $this->info("No enabled journals found to harvest.");
            return;
        }

        $this->info("Found {$journals->count()} journal(s) to harvest.");

        foreach ($journals as $journal) {
            $this->info("Dispatching harvest job for: {$journal->title}");
            HarvestJournalJob::dispatch($journal);
        }

        $this->info("Harvesting jobs dispatched!");
    }
}
