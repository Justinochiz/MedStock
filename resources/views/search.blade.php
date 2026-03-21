@extends('layouts.base')
@section('body')
    @php
        $typeLabel = $type === 'product' ? 'Products' : ($type === 'service' ? 'Services' : 'All');
        $productCount = $products ? $products->total() : 0;
        $serviceCount = $services ? $services->total() : 0;
        $resultCount = $productCount + $serviceCount;
    @endphp

    <div class="container py-4">
        <h1 class="mb-2">Search</h1>
        <p class="text-muted mb-4">
            Showing {{ $resultCount }} result(s)
            @if(!empty($term))
                for "{{ $term }}"
            @endif
            (Filter: {{ $typeLabel }})
        </p>

        @if(empty($term))
            <div class="alert alert-info">Please enter a search term.</div>
        @elseif($resultCount === 0)
            <div class="alert alert-warning">No results found for your search.</div>
        @endif

        @if($products && $products->count() > 0)
            <h2 class="h4 mt-4">Products</h2>

            <ul class="list-group mb-4">
                @foreach ($products as $product)
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('items.show', $product->item_id) }}" class="fw-semibold text-decoration-none">
                            {{ $product->description }}
                        </a>
                        <small class="text-muted">
                            {{ $product->category }} | PHP {{ number_format((float) $product->sell_price, 2) }}
                        </small>
                    </li>
                @endforeach
            </ul>

            <div class="mb-4">
                {{ $products->appends(request()->except('products_page'))->links() }}
            </div>
        @endif

        @if($services && $services->count() > 0)
            <h2 class="h4 mt-4">Services</h2>

            <ul class="list-group mb-4">
                @foreach ($services as $service)
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('shop.services.show', $service->service_id) }}" class="fw-semibold text-decoration-none">
                            {{ $service->name }}
                        </a>
                        <small class="text-muted">
                            PHP {{ number_format((float) $service->price, 2) }}
                        </small>
                    </li>
                @endforeach
            </ul>

            <div class="mb-4">
                {{ $services->appends(request()->except('services_page'))->links() }}
            </div>
        @endif
    </div>
@endsection