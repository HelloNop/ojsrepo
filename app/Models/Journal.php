<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'publisher_id',
        'oai_base_url',
        'last_harvested_at',
        'harvest_started_at',
        'enabled',
        'set_spec',
        'description',
        'slug',
        'cover_image',
        'issn',
        'eissn',
        'website_url',
    ];

    protected function casts(): array
    {
        return [
            'last_harvested_at' => 'datetime',
            'harvest_started_at' => 'datetime',
            'enabled' => 'boolean',
        ];
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function harvestLogs()
    {
        return $this->hasMany(HarvestLog::class);
    }

    // Harvest Helper Methods
    public function isHarvesting(): bool
    {
        return $this->harvest_started_at !== null;
    }

    public function canStartHarvest(): bool
    {
        // Allow harvest if not currently harvesting
        // Or if harvest started more than 2 hours ago (likely stuck)
        if (! $this->isHarvesting()) {
            return true;
        }

        return $this->harvest_started_at->diffInHours(now()) > 2;
    }

    public function startHarvest(): void
    {
        $this->update(['harvest_started_at' => now()]);
    }

    public function finishHarvest(): void
    {
        $this->update([
            'harvest_started_at' => null,
            'last_harvested_at' => now(),
        ]);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
