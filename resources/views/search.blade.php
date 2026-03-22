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

        <form action="{{ route('search') }}" method="GET" class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Search Term</label>
                        <input type="text" name="term" class="form-control" value="{{ $term }}" placeholder="Product or service name">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">Type</label>
                        <select name="type" class="form-select">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All</option>
                            <option value="product" {{ $type === 'product' ? 'selected' : '' }}>Products</option>
                            <option value="service" {{ $type === 'service' ? 'selected' : '' }}>Services</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">Min Price</label>
                        <input type="number" name="min_price" class="form-control" min="0" step="0.01" value="{{ old('min_price', $minPrice) }}" placeholder="0.00">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">Max Price</label>
                        <input type="number" name="max_price" class="form-control" min="0" step="0.01" value="{{ old('max_price', $maxPrice) }}" placeholder="99999.00">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Category (Products)</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach ($categories as $categoryOption)
                                <option value="{{ $categoryOption }}" {{ $category === $categoryOption ? 'selected' : '' }}>
                                    {{ $categoryOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($hasItemBrandColumn)
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">Brand (Products)</label>
                            <select name="brand" class="form-select">
                                <option value="">All Brands</option>
                                @foreach ($brands as $brandOption)
                                    <option value="{{ $brandOption }}" {{ $brand === $brandOption ? 'selected' : '' }}>
                                        {{ $brandOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($hasServiceTypeColumn)
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">Type (Services)</label>
                            <select name="service_type" class="form-select">
                                <option value="">All Service Types</option>
                                @foreach ($serviceTypes as $serviceTypeOption)
                                    <option value="{{ $serviceTypeOption }}" {{ $serviceType === $serviceTypeOption ? 'selected' : '' }}>
                                        {{ $serviceTypeOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-lg-3 col-md-6 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">Apply Filters</button>
                        <a href="{{ route('search') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </div>
            </div>
        </form>

        @if(empty($term) && !$hasActiveFilters)
            <div class="alert alert-info">Please enter a search term or apply filters.</div>
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
                            {{ $product->category }}
                            @if(isset($product->brand) && !empty($product->brand))
                                | {{ $product->brand }}
                            @endif
                            | PHP {{ number_format((float) $product->sell_price, 2) }}
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
                            @if(isset($service->type) && !empty($service->type))
                                {{ $service->type }} |
                            @endif
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