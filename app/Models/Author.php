<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'affiliation',
        'email',
        'orcid',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'author_article_pivots');
    }
}
