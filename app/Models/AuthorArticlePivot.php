<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorArticlePivot extends Model
{
    protected $fillable = [
        'author_id',
        'article_id',
        'order',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
