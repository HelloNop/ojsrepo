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
                 <li>
                    <span class="text-gray-300">/</span>
                </li>
                <li>
                    <a href="{{ route('journals.index') }}" class="hover:text-gray-900 transition-colors">Journals</a>
                </li>
                <li>
                    <span class="text-gray-300">/</span>
                </li>
                <li>
                    <span class="text-gray-900 font-medium truncate max-w-[200px]">{{ $journal->title }}</span>
                </li>
            </ol>
        </nav>

        <!-- Journal Header -->
        <div class="border-b border-gray-200 pb-10 mb-10">
            <div class="flex flex-col md:flex-row gap-8 items-start">
                <!-- Cover Image -->
                <div class="flex-shrink-0 w-40 h-56 bg-gray-100 border border-gray-200 shadow-sm relative">
                     @if($journal->cover_image)
                        <img src="{{ Storage::url($journal->cover_image) }}" alt="{{ $journal->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-50 text-gray-400">
                             <span class="text-xs uppercase tracking-widest text-center px-1">No Cover</span>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1">
                     <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2 leading-tight">{{ $journal->title }}</h1>
                     
                     <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600 mb-6 font-sans">
                        @if($journal->publisher)
                            <span class="flex items-center">
                                <span class="font-bold text-gray-900 mr-2">Publisher:</span> {{ $journal->publisher->name }}
                            </span>
                        @endif
                        @if($journal->issn)
                            <span class="flex items-center">
                                <span class="font-bold text-gray-900 mr-2">ISSN:</span> {{ $journal->issn }}
                            </span>
                        @endif
                         @if($journal->eissn)
                            <span class="flex items-center">
                                <span class="font-bold text-gray-900 mr-2">e-ISSN:</span> {{ $journal->eissn }}
                            </span>
                        @endif
                    </div>

                    @if($journal->description)
                        <div class="prose prose-lg text-gray-700 max-w-none mb-6 leading-relaxed font-serif">
                            <p>{{ $journal->description }}</p>
                        </div>
                    @endif

                     @if($journal->oai_base_url)
                        <div class="mt-4 font-sans">
                            <a href="{{ $journal->oai_base_url }}" target="_blank" class="inline-flex items-center text-sm font-medium text-indigo-700 hover:text-indigo-900 border-b border-indigo-200 hover:border-indigo-900 transition-colors pb-0.5">
                                Visit Original Journal Website
                                <svg class="ml-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <!-- Sidebar: Issues -->
            <div class="lg:col-span-1 pr-0 lg:pr-8">
                 <div class="sticky top-20 border border-gray-200 p-6 rounded-lg">
                    <h3 class="font-bold text-gray-900 mb-4 uppercase tracking-wider text-xs font-sans">
                        Browse Issues
                        @if(request('issue'))
                            <a href="{{ route('journals.show', $journal) }}" class="ml-2 text-indigo-600 hover:underline normal-case float-right">Clear</a>
                        @endif
                    </h3>
                    
                    @if($issues->count() > 0)
                        <div id="issues-container" class="transition-all duration-300">
                            <ul class="space-y-2 font-sans text-sm" id="issues-list">
                               @foreach($issues as $index => $issue)
                                    <li class="issue-item {{ $index >= 10 ? 'hidden' : '' }}">
                                        <a href="{{ route('journals.show', [$journal, 'issue' => $issue->id]) }}" 
                                           class="block px-3 py-2 rounded-md transition-colors {{ request('issue') == $issue->id ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                            {{ $issue->title }} ({{ $issue->year }})
                                        </a>
                                    </li>
                               @endforeach
                            </ul>
                        </div>
                        
                        @if($issues->count() > 10)
                            <button 
                                id="toggle-issues" 
                                class="mt-4 w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-indigo-700 hover:text-indigo-900 hover:bg-indigo-50 rounded-md transition-colors"
                                onclick="toggleIssues()"
                            >
                                <span id="toggle-text">Show More</span>
                                <svg id="toggle-icon" class="w-4 h-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <style>
                                #issues-container.scrollable {
                                    max-height: 500px;
                                    overflow-y: auto;
                                    padding-right: 4px;
                                }
                                
                                #issues-container.scrollable::-webkit-scrollbar {
                                    width: 6px;
                                }
                                
                                #issues-container.scrollable::-webkit-scrollbar-track {
                                    background: #f1f1f1;
                                    border-radius: 3px;
                                }
                                
                                #issues-container.scrollable::-webkit-scrollbar-thumb {
                                    background: #c7d2fe;
                                    border-radius: 3px;
                                }
                                
                                #issues-container.scrollable::-webkit-scrollbar-thumb:hover {
                                    background: #a5b4fc;
                                }
                            </style>
                            
                            <script>
                                function toggleIssues() {
                                    const container = document.getElementById('issues-container');
                                    const items = document.querySelectorAll('.issue-item');
                                    const toggleText = document.getElementById('toggle-text');
                                    const toggleIcon = document.getElementById('toggle-icon');
                                    const isExpanded = toggleText.textContent === 'Show Less';
                                    
                                    items.forEach((item, index) => {
                                        if (index >= 10) {
                                            item.classList.toggle('hidden');
                                        }
                                    });
                                    
                                    if (isExpanded) {
                                        toggleText.textContent = 'Show More';
                                        toggleIcon.style.transform = 'rotate(0deg)';
                                        container.classList.remove('scrollable');
                                    } else {
                                        toggleText.textContent = 'Show Less';
                                        toggleIcon.style.transform = 'rotate(180deg)';
                                        container.classList.add('scrollable');
                                    }
                                }
                            </script>
                        @endif
                    @else
                        <p class="text-sm text-gray-500 italic font-sans">No issues available.</p>
                    @endif
                 </div>
            </div>

            <!-- Main Content: Articles -->
            <div class="lg:col-span-3">
                 <div class="border-b border-gray-200 pb-4 mb-8 flex items-baseline justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">
                        @if(request('issue'))
                            Articles in Issue
                        @else
                            Latest Articles
                        @endif
                    </h2>
                    <span class="text-sm text-gray-500 font-sans">{{ $articles->total() }} Items</span>
                </div>
                
                <div class="space-y-10">
                    @forelse($articles as $article)
                        <article class="group">
                             <div class="text-sm text-gray-500 mb-1 font-sans flex gap-2 items-center">
                                @if($article->issue)
                                    <span class="font-medium text-gray-700">{{ $article->issue->title }}</span>
                                    <span>&bull;</span>
                                @endif
                                <span>{{ \Carbon\Carbon::parse($article->published_date)->format('F Y') }}</span>
                            </div>

                            <h3 class="text-xl font-bold text-gray-900 mb-2 leading-snug group-hover:text-indigo-800 transition-colors">
                                <a href="{{ route('articles.show', $article) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            
                            <div class="text-gray-700 italic mb-3 text-sm">
                                @foreach($article->authors->take(5) as $author)
                                    {{ $author->name }}@if(!$loop->last), @endif
                                @endforeach
                                @if($article->authors->count() > 5)
                                    et al.
                                @endif
                            </div>

                            <div class="text-gray-600 leading-relaxed mb-4 line-clamp-3 font-serif">
                                {{ $article->abstract }}
                            </div>

                            <div class="flex items-center gap-4 font-sans text-sm">
                                <a href="{{ route('articles.show', $article) }}" class="text-indigo-700 font-medium hover:underline">
                                    Abstract
                                </a>
                                @if($article->pdf_url)
                                    <a href="{{ $article->pdf_url }}" target="_blank" class="text-indigo-700 font-medium hover:underline flex items-center">
                                        PDF
                                        <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                @endif
                                @if($article->doi)
                                    <span class="text-gray-400">|</span>
                                    <span class="text-gray-500">DOI: {{ $article->doi }}</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="py-12 text-center text-gray-500 font-serif italic border border-dashed border-gray-200 rounded-lg">
                            No articles found in this collection.
                        </div>
                    @endforelse
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200">
                    {{ $articles->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
