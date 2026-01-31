<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_id',
        'issue_id',
        'title',
        'abstract',
        'keywords',
        'source_url',
        'pdf_url',
        'published_date',
        'doi',
        'oai_id',
        'pages',
        'slug',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_article_pivots');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
