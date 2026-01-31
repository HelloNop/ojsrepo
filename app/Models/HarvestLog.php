<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestLog extends Model
{
    protected $fillable = [
        'journal_id',
        'status',
        'started_at',
        'completed_at',
        'records_processed',
        'records_failed',
        'total_records',
        'error_message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
            'records_processed' => 'integer',
            'records_failed' => 'integer',
            'total_records' => 'integer',
        ];
    }

    // Relationships
    public function journal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    // Scopes
    public function scopeRunning($query): void
    {
        $query->where('status', 'running');
    }

    public function scopeSuccess($query): void
    {
        $query->where('status', 'success');
    }

    public function scopeFailed($query): void
    {
        $query->whereIn('status', ['failed', 'partial']);
    }

    public function scopeRecent($query): void
    {
        $query->orderBy('started_at', 'desc');
    }

    // Helper Methods
    public function markAsRunning(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function markAsSuccess(int $recordsProcessed, int $recordsFailed = 0): void
    {
        $this->update([
            'status' => 'success',
            'completed_at' => now(),
            'records_processed' => $recordsProcessed,
            'records_failed' => $recordsFailed,
            'total_records' => $recordsProcessed + $recordsFailed,
        ]);
    }

    public function markAsFailed(string $errorMessage, int $recordsProcessed = 0, int $recordsFailed = 0): void
    {
        $this->update([
            'status' => $recordsProcessed > 0 ? 'partial' : 'failed',
            'completed_at' => now(),
            'records_processed' => $recordsProcessed,
            'records_failed' => $recordsFailed,
            'total_records' => $recordsProcessed + $recordsFailed,
            'error_message' => $errorMessage,
        ]);
    }

    public function updateProgress(int $recordsProcessed, int $recordsFailed = 0): void
    {
        $this->update([
            'records_processed' => $recordsProcessed,
            'records_failed' => $recordsFailed,
            'total_records' => $recordsProcessed + $recordsFailed,
        ]);
    }

    public function getDurationAttribute(): ?int
    {
        if (! $this->started_at || ! $this->completed_at) {
            return null;
        }

        return $this->started_at->diffInSeconds($this->completed_at);
    }

    public function getFormattedDurationAttribute(): ?string
    {
        if (! $this->duration) {
            return null;
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'partial']);
    }
}
