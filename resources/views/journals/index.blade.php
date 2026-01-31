@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen font-serif">
    <!-- Academic Header -->
    <div class="border-b border-gray-200 bg-gray-50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Journal Collections</h1>
            <p class="mt-4 max-w-3xl text-lg text-gray-600 leading-relaxed">
                Access peer-reviewed research and scholarly publications across multiple disciplines.
            </p>
        </div>
    </div>

    <!-- Content -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            @forelse($journals as $journal)
                <div class="flex flex-col sm:flex-row gap-6 border-b border-gray-100 pb-8 last:border-0 hover:bg-gray-50 transition-colors p-4 -mx-4 rounded-lg">
                    <!-- Cover Image -->
                    <div class="flex-shrink-0 w-full sm:w-32 h-48 bg-gray-100 border border-gray-200 shadow-sm relative">
                        @if($journal->cover_image)
                            <img src="{{ Storage::url($journal->cover_image) }}" alt="{{ $journal->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-50 text-gray-400">
                                <span class="text-xs uppercase tracking-widest text-center px-2">No Cover</span>
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2 font-serif leading-tight">
                             <a href="{{ route('journals.show', $journal) }}" class="hover:text-indigo-800 hover:underline">
                                {{ $journal->title }}
                             </a>
                        </h3>
                        
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600 mb-4 font-sans">
                            @if($journal->publisher)
                                <span class="flex items-center">
                                    <span class="font-semibold text-gray-900 mr-1">Publisher:</span> {{ $journal->publisher->name }}
                                </span>
                            @endif
                            @if($journal->issn)
                                <span class="flex items-center">
                                    <span class="font-semibold text-gray-900 mr-1">ISSN:</span> {{ $journal->issn }}
                                </span>
                            @endif
                        </div>

                        <p class="text-gray-700 text-base leading-relaxed mb-4 line-clamp-3 font-sans">
                             {{ $journal->description ?? 'No description available.' }}
                        </p>

                        <div class="flex items-center justify-between mt-auto pt-2">
                             <div class="text-sm font-sans">
                                <span class="text-gray-500">{{ $journal->articles_count }} Articles</span>
                            </div>
                            
                            <a href="{{ route('journals.show', $journal) }}" class="text-sm font-semibold text-indigo-800 hover:text-indigo-600 font-sans uppercase tracking-wide">
                                View Journal &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 border border-dashed border-gray-300 rounded-lg">
                    <p class="text-gray-500 text-lg font-serif italic">No journals available in the catalog.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
