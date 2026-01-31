@extends('layouts.app')

@section('content')
<div class="relative bg-white isolate overflow-hidden">
    <!-- Hero Section -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 pt-20 pb-24 text-center">
        <h1 class="text-4xl font-bold tracking-tight font-serif text-gray-900 sm:text-6xl">Dinasti Journal Repository</h1>
        <p class="mt-6 text-lg leading-8 text-gray-600">
            Discover thousands of open access articles, journals, and research papers from our community.
        </p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            <form action="{{ route('articles.index') }}" method="GET" class="w-full max-w-lg">
                <div class="relative flex items-center">
                    <input type="text" name="search" class="block w-full rounded-lg border-0 px-4 py-4 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-lg sm:leading-6" placeholder="Search for research...">
                    <button type="submit" class="absolute right-2 rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Background pattern -->
    <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
        <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>
</div>

<!-- Stats Section -->
<div class="bg-gray-50 py-12 sm:py-16">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <dl class="grid grid-cols-1 gap-x-8 gap-y-16 text-center lg:grid-cols-3">
            <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                <dt class="text-base leading-7 text-gray-600">Articles Indexed</dt>
                <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{ number_format($stats['articles']) }}</dd>
            </div>
            <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                <dt class="text-base leading-7 text-gray-600">Journals Indexed</dt>
                <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{ number_format($stats['journals']) }}</dd>
            </div>
            <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                <dt class="text-base leading-7 text-gray-600">Active Authors</dt>
                <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{ number_format($stats['authors']) }}</dd>
            </div>
        </dl>
    </div>
</div>

<!-- Latest Articles -->
<div class="bg-white py-14 sm:py-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight font-serif text-gray-900 sm:text-4xl">Recent Articles</h2>
            <p class="mt-2 text-lg leading-8 text-gray-600">The latest research added to our repository.</p>
        </div>
        <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
            @foreach($latestArticles as $article)
                <article class="flex flex-col items-start justify-between">
                    <div class="group relative">
                        <h3 class="mt-3 text-lg font-semibold font-serif leading-6 text-gray-900 group-hover:text-gray-600">
                            <a href="{{ route('articles.show', $article) }}">
                                <span class="absolute inset-0"></span>
                                {{ $article->title }}
                            </a>
                        </h3>
                        <p class="mt-5 line-clamp-3 text-sm leading-6 text-gray-600 font-serif">{{ Str::limit($article->abstract, 150) }}</p>
                    </div>
                    <div class="relative mt-4 flex items-center gap-x-4">
                        <div class="text-sm leading-6">
                            <p class="font-semibold text-gray-900">
                                @foreach($article->authors->take(2) as $author)
                                    <span class="mr-1">{{ $author->name }}</span>
                                    @if(!$loop->last), @endif
                                @endforeach
                                @if($article->authors->count() > 2)
                                    <span class="text-gray-500 italic">+{{ $article->authors->count() - 2 }} others</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        
        <div class="mt-12 text-center">
             <a href="{{ route('articles.index') }}" class="text-sm font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                Browse all articles <span aria-hidden="true">→</span>
             </a>
        </div>
    </div>
</div>
@endsection
