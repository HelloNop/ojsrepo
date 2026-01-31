@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-10">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Home</span>
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                        </svg>
                        <a href="{{ route('articles.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Articles</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                        </svg>
                        <span class="ml-4 text-sm font-medium text-gray-500" aria-current="page">{{ $author->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white px-6 py-6 lg:px-8 mb-8 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100">
                        <span class="text-xl font-medium leading-none text-indigo-700">{{ substr($author->name, 0, 1) }}</span>
                    </span>
                </div>
                <div class="ml-5">
                    <h1 class="text-3xl font-bold tracking-tight font-serif text-gray-900 sm:text-4xl">{{ $author->name }}</h1>
                    @if($author->affiliation)
                        <p class="mt-2 text-lg text-gray-600">{{ $author->affiliation }}</p>
                    @endif
                    <div class="mt-2 text-sm text-gray-500">
                        <span>{{ $articles->total() }}</span> Articles Found
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-4">
            <div class="lg:col-span-4">
                <div class="space-y-6">
                    @forelse($articles as $article)
                        <div class="flex flex-col overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center gap-x-4 text-xs">
                                    <time datetime="{{ $article->published_date }}" class="text-gray-500">{{ \Carbon\Carbon::parse($article->published_date)->format('M d, Y') }}</time>
                                    <a href="{{ route('journals.show', $article->journal) }}" class="relative z-10 rounded-full bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100">{{ $article->journal->title }}</a>
                                </div>
                                <div class="group relative">
                                    <h3 class="mt-3 text-lg font-semibold leading-6 font-serif text-gray-900 group-hover:text-gray-600">
                                        <a href="{{ route('articles.show', $article) }}">
                                            <span class="absolute inset-0"></span>
                                            {{ $article->title }}
                                        </a>
                                    </h3>
                                    <p class="mt-5 line-clamp-3 text-sm leading-6 text-gray-600 font-serif">{{ $article->abstract }}</p>
                                </div>
                                <div class="relative mt-8 flex items-center gap-x-4">
                                    <div class="text-sm leading-6">
                                        <p class="font-semibold text-gray-900">
                                            @foreach($article->authors as $a)
                                                <span class="{{ $a->id === $author->id ? 'text-indigo-600 font-bold' : '' }}">
                                                    {{ $a->name }}
                                                </span>@if(!$loop->last), @endif
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No articles found</h3>
                            <p class="mt-1 text-sm text-gray-500">This author doesn't have any articles yet.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $articles->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
