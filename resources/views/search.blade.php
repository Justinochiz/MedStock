@extends('layouts.base')
@section('body')
    <div class="container py-4">
        <h1 class="mb-2">Search</h1>
        <p class="text-muted mb-4">
            Showing {{ $searchResults->count() }} result(s)
            @if(!empty($term))
                for "{{ $term }}"
            @endif
            @php
                $typeLabel = $type === 'product' ? 'Products' : ($type === 'service' ? 'Services' : 'All');
            @endphp
            (Filter: {{ $typeLabel }})
        </p>

        @if(empty($term))
            <div class="alert alert-info">Please enter a search term.</div>
        @elseif($searchResults->count() === 0)
            <div class="alert alert-warning">No results found for your search.</div>
        @endif

        @foreach ($searchResults->groupByType() as $resultType => $modelSearchResults)
            <h2 class="h4 mt-4">{{ $resultType }}</h2>

            @foreach ($modelSearchResults as $searchResult)
                <ul>
                    <a href="{{ $searchResult->url }}">{{ $searchResult->title }}</a>
                </ul>
            @endforeach
        @endforeach
    </div>
@endsection