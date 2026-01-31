@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen font-serif">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-sm font-sans" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-2 text-gray-500">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-gray-900 transition-colors">Home</a>
                </li>
                 <li><span class="text-gray-300">/</span></li>
                <li>
                    <a href="{{ route('articles.index') }}" class="hover:text-gray-900 transition-colors">Articles</a>
                </li>
                 <li><span class="text-gray-300">/</span></li>
                <li>
                    <span class="text-gray-900 font-medium truncate max-w-[200px]">{{ $article->title }}</span>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Article Content -->
            <div class="lg:col-span-2">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                    {{ $article->title }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-x-1 gap-y-2 mb-8 text-sm font-sans text-gray-600">
                    <span class="font-bold text-gray-900">Authors:</span>
                    @foreach($article->authors as $author)
                        <div class="flex items-center">
                            <a href="{{ route('authors.show', $author) }}" class="text-indigo-800 hover:underline">
                                {{ $author->name }}
                            </a>
                            @if($author->affiliation)
                                <span class="text-gray-500 ml-1">({{ $author->affiliation }})</span>
                            @endif
                            @if(!$loop->last)
                                <span class="text-gray-400 mx-2">;</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mb-10">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest font-sans mb-4 border-b border-gray-100 pb-2">Abstract</h2>
                    <div class="prose prose-lg text-gray-700 max-w-none font-serif leading-relaxed">
                        <p>{{ $article->abstract }}</p>
                    </div>
                </div>

                @if($article->keywords)
                <div class="mb-10">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest font-sans mb-3">Keywords</h2>
                    <div class="flex flex-wrap gap-2 font-sans">
                        @foreach(explode(';', $article->keywords) as $keyword)
                            @if(trim($keyword))
                                <span class="inline-flex items-center rounded bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-700 border border-gray-200">
                                    {{ trim($keyword) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar Metadata -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-6 font-sans">
                    <h3 class="font-bold text-gray-900 uppercase tracking-widest text-xs mb-6">Article Details</h3>
                    
                    <dl class="space-y-4 text-sm">
                        <div class="pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                            <dt class="text-xs text-gray-500 uppercase tracking-wide mb-1">Journal</dt>
                            <dd class="font-medium">
                                <a href="{{ route('journals.show', $article->journal) }}" class="text-indigo-800 hover:underline">{{ $article->journal->title }}</a>
                            </dd>
                        </div>
                        
                        <div class="pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                            <dt class="text-xs text-gray-500 uppercase tracking-wide mb-1">Published</dt>
                            <dd class="text-gray-900">{{ \Carbon\Carbon::parse($article->published_date)->format('F d, Y') }}</dd>
                        </div>
                        
                        @if($article->issue)
                        <div class="pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                            <dt class="text-xs text-gray-500 uppercase tracking-wide mb-1">Issue</dt>
                            <dd class="text-gray-900">
                                <a href="{{ route('journals.show', [$article->journal, 'issue' => $article->issue_id]) }}" class="hover:underline hover:text-indigo-800">
                                    {{ $article->issue->title }} ({{ $article->issue->year }})
                                </a>
                            </dd>
                        </div>
                        @endif

                        @if($article->doi)
                        <div class="pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                            <dt class="text-xs text-gray-500 uppercase tracking-wide mb-1">DOI</dt>
                            <dd class="break-all font-mono text-xs">
                                <a href="https://doi.org/{{ $article->doi }}" target="_blank" class="text-indigo-800 hover:underline">
                                    {{ $article->doi }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        
                        @if($article->pages)
                        <div class="pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                            <dt class="text-xs text-gray-500 uppercase tracking-wide mb-1">Pages</dt>
                            <dd class="text-gray-900">{{ $article->pages }}</dd>
                        </div>
                        @endif
                    </dl>

                    <div class="mt-8 space-y-3">
                        @if($article->pdf_url)
                             <a href="{{ $article->pdf_url }}" target="_blank" class="flex w-full items-center justify-center rounded-md bg-indigo-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download PDF
                            </a>
                        @endif
                        
                        <a href="{{ $article->source_url }}" target="_blank" class="flex w-full items-center justify-center rounded-md bg-white border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                            View Original Source
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
