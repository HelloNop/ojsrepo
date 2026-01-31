<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Journal;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $stats = [
            'articles' => Article::count(),
            'journals' => Journal::where('enabled', true)->count(),
            'authors' => \App\Models\Author::count(),
        ];

        $latestArticles = Article::with(['journal', 'authors'])
            ->latest('published_date')
            ->take(6)
            ->get();

        return view('home', compact('stats', 'latestArticles'));
    }

    public function browse(Request $request)
    {
        $query = Article::with(['journal', 'authors']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhereHas('authors', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('journal')) {
            $query->where('journal_id', $request->get('journal'));
        }

        if ($request->has('year')) {
            $query->whereYear('published_date', $request->get('year'));
        }

        $articles = $query->latest('published_date')->paginate(12)->withQueryString();
        
        $journals = Journal::whereHas('articles')->get();
        
        $isSqlite = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite';
        $yearSql = $isSqlite ? "strftime('%Y', published_date)" : 'YEAR(published_date)';
        
        $years = Article::selectRaw("$yearSql as year")
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('articles.index', compact('articles', 'journals', 'years'));
    }

    public function article(Article $article)
    {
        $article->load(['journal', 'authors', 'issue']);
        return view('articles.show', compact('article'));
    }

    public function journals()
    {
        $journals = Journal::where('enabled', true)
            ->withCount('articles')
            ->latest()
            ->get();

        return view('journals.index', compact('journals'));
    }

    public function journal(Request $request, Journal $journal)
    {
        $query = $journal->articles()
            ->with(['authors', 'issue']);

        if ($request->has('issue')) {
            $query->where('issue_id', $request->get('issue'));
        }

        $articles = $query->latest('published_date')
            ->paginate(12)
            ->withQueryString();

        $issues = $journal->issues()
            ->orderBy('year', 'desc')
            ->orderBy('title', 'desc') // Assuming Volume/Issue allows checking title, often users put Vol 1 No 1
            ->get();

        return view('journals.show', compact('journal', 'articles', 'issues'));
    }

    public function author(\App\Models\Author $author)
    {
        $articles = $author->articles()
            ->with(['journal', 'authors'])
            ->latest('published_date')
            ->paginate(12);

        return view('authors.show', compact('author', 'articles'));
    }
}
