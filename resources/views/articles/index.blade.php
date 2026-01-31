@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen font-serif">
    <!-- Academic Header -->
    <div class="border-b border-gray-200 bg-gray-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Browse Articles</h1>
            <p class="mt-2 text-gray-600 font-sans">
                Search and filter through our extensive collection of scholarly research.
            </p>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 gap-x-12 gap-y-10 lg:grid-cols-4">
            <!-- Sidebar / Filters -->
            <form action="{{ route('articles.index') }}" method="GET" class="hidden lg:block lg:col-span-1">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="border-b border-gray-100 pb-8 mb-8">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest font-sans mb-4">Journals</h3>
                    <div class="space-y-3 font-sans text-sm">
                        @foreach($journals as $journal)
                            <div class="flex items-start">
                                <input id="journal-{{ $journal->id }}" name="journal" value="{{ $journal->id }}" type="radio" {{ request('journal') == $journal->id ? 'checked' : '' }} class="mt-1 h-3 w-3 border-gray-300 text-indigo-800 focus:ring-indigo-800" onchange="this.form.submit()">
                                <label for="journal-{{ $journal->id }}" class="ml-3 text-gray-600 hover:text-gray-900 cursor-pointer">{{ $journal->title }}</label>
                            </div>
                        @endforeach
                        
                        @if(request('journal'))
                            <div class="pt-2">
                                <a href="{{ route('articles.index', request()->except('journal')) }}" class="text-xs text-indigo-800 hover:underline font-medium">Clear Journal Filter</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="border-b border-gray-100 pb-8">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest font-sans mb-4">Publication Year</h3>
                    <div class="space-y-2 font-sans text-sm max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($years as $year)
                            <div class="flex items-center">
                                <input id="year-{{ $year }}" name="year" value="{{ $year }}" type="radio" {{ request('year') == $year ? 'checked' : '' }} class="h-3 w-3 border-gray-300 text-indigo-800 focus:ring-indigo-800" onchange="this.form.submit()">
                                <label for="year-{{ $year }}" class="ml-3 text-gray-600 hover:text-gray-900 cursor-pointer">{{ $year }}</label>
                            </div>
                        @endforeach
                        
                        @if(request('year'))
                            <div class="pt-2">
                                <a href="{{ route('articles.index', request()->except('year')) }}" class="text-xs text-indigo-800 hover:underline font-medium">Clear Year Filter</a>
                            </div>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                @if(request('search'))
                    <div class="mb-8 flex items-center justify-between bg-gray-50 border border-gray-100 rounded-lg p-4 font-sans">
                        <p class="text-sm text-gray-600">Results for: <span class="font-bold text-gray-900">"{{ request('search') }}"</span></p>
                        <a href="{{ route('articles.index') }}" class="text-sm font-medium text-indigo-800 hover:underline">Clear Search</a>
                    </div>
                @endif

                <div class="space-y-10">
                    @forelse($articles as $article)
                        <article class="group pb-8 border-b border-gray-100 last:border-0">
                            <div class="text-sm text-gray-500 mb-2 font-sans flex flex-wrap gap-2 items-center">
                                <span class="font-medium text-gray-800">{{ $article->journal->title }}</span>
                                <span class="text-gray-300">|</span>
                                <span>{{ \Carbon\Carbon::parse($article->published_date)->format('F Y') }}</span>
                                @if($article->issue)
                                    <span class="text-gray-300">|</span>
                                    <span>{{ $article->issue->title }}</span>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-gray-900 mb-3 leading-snug">
                                <a href="{{ route('articles.show', $article) }}" class="hover:text-indigo-800 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            <div class="text-gray-600 text-base leading-relaxed mb-3 line-clamp-3">
                                {{ $article->abstract }}
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <div class="text-sm text-gray-700 italic font-sans truncate pr-4 max-w-lg">
                                    @foreach($article->authors->take(3) as $author)
                                        {{ $author->name }}@if(!$loop->last), @endif
                                    @endforeach
                                    @if($article->authors->count() > 3)
                                        et al.
                                    @endif
                                </div>

                                <div class="flex items-center gap-4 font-sans text-sm flex-shrink-0">
                                    <a href="{{ route('articles.show', $article) }}" class="text-indigo-800 font-medium hover:underline">
                                        View Abstract
                                    </a>
                                    @if($article->pdf_url)
                                        <a href="{{ $article->pdf_url }}" target="_blank" class="text-gray-600 hover:text-indigo-800 flex items-center transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            PDF
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="text-center py-20 border border-dashed border-gray-200 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 font-sans">No articles found</h3>
                            <p class="mt-1 text-gray-500 font-sans max-w-sm mx-auto">Try adjusting your search or filters to find what you're looking for.</p>
                            <div class="mt-6 font-sans">
                                <a href="{{ route('articles.index') }}" class="text-sm font-semibold text-indigo-800 hover:underline">
                                    Reset all filters
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-12 pt-6 border-t border-gray-100 font-sans">
                    {{ $articles->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
