@extends('layouts.base')
@section('title')
    Medical Equipment Shop
@endsection
@section('body')
    @include('layouts.flash-messages')

    <div class="container-fluid py-5 shop-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-12">
                    <p class="text-uppercase small fw-semibold text-light-emphasis mb-2">Online Medical Store</p>
                    <h1 class="text-white mb-3">
                        <i class="fas fa-hospital-user"></i> Shop Medical Essentials
                    </h1>
                    <p class="text-white-50 mb-0">Browse trusted equipment, choose your quantity, and checkout with confidence.</p>
                    <a href="{{ route('shop.services') }}" class="btn btn-light mt-3">
                        <i class="fas fa-briefcase-medical me-2"></i>Browse Medical Services
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <form action="{{ route('search') }}" method="GET" class="input-group input-group-lg">
                    @csrf
                    <input type="text" class="form-control" name="term" placeholder="Search equipment by name..." 
                           value="{{ request('term') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
            </div>
        </div>

        <!-- Items Grid -->
        @if($items->count() > 0)
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Featured Products</h4>
                <span class="text-muted small">{{ $items->count() }} product(s) available</span>
            </div>

            <div class="row">
                @foreach ($items as $item)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 product-card" style="transition: all 0.3s ease;">
                            <!-- Product Image -->
                            <div class="position-relative" style="height: 300px; overflow: hidden; background-color: #f8f9fa;">
                                @php
                                    $gallery = [];
                                    if (!empty($item->gallery_paths)) {
                                        $decodedGallery = json_decode($item->gallery_paths, true);
                                        if (is_array($decodedGallery)) {
                                            $gallery = $decodedGallery;
                                        }
                                    }

                                    $primaryPath = $gallery[0] ?? $item->img_path;
                                    $storagePath = str_replace('public/', '', (string) $primaryPath);
                                    $hasImage = !empty($storagePath) && Storage::disk('public')->exists($storagePath);
                                    $imageUrl = $hasImage
                                        ? Storage::url($storagePath)
                                        : asset('images/medstock-logo.png');
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $item->description }}" 
                                     class="card-img-top" style="width: 100%; height: 100%; object-fit: cover;">
                                <div class="position-absolute top-0 start-0 m-3">
                                    @php $availableQty = (int) ($item->quantity ?? 0); @endphp
                                    @if($availableQty > 0)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> In Stock
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle"></i> Out of Stock
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-dark fw-bold">{{ $item->description }}</h5>
                                
                                <div class="my-3">
                                    <div class="text-end">
                                        <small class="text-muted d-block">Selling Price</small>
                                        <p class="h5 text-danger mb-0">P{{ number_format($item->sell_price, 2) }}</p>
                                    </div>
                                </div>

                                <!-- Stock Status -->
                                <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 0.9rem;">
                                    <i class="fas fa-warehouse"></i> {{ $availableQty }} item(s) available
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-auto">
                                    @if($availableQty > 0)
                                        <form action="{{ route('addToCart', $item->item_id) }}" method="GET" class="mb-2">
                                            <div class="d-flex gap-2 align-items-center">
                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    min="1"
                                                    max="{{ $availableQty }}"
                                                    value="1"
                                                    class="form-control"
                                                    style="max-width: 100px;"
                                                    required
                                                >
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                </button>
                                                <button type="submit" formaction="{{ route('buyNow', $item->item_id) }}" class="btn btn-success">
                                                    <i class="fas fa-bolt"></i> Buy Now
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-secondary w-100 mb-2" disabled>
                                            Out of Stock
                                        </button>
                                    @endif
                                    <a href="{{ route('items.show', $item->item_id) }}" 
                                       class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($items instanceof \Illuminate\Pagination\Paginator)
                <div class="d-flex justify-content-center mt-5">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: #ccc;"></i>
                        <h3 class="mt-3 text-muted">No Equipment Available</h3>
                        <p class="text-secondary">Check back soon for our medical equipment collection</p>
                        <a href="/" class="btn btn-primary mt-3">
                            <i class="fas fa-home"></i> Return to Home
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="p-4">
                        <i class="fas fa-truck text-primary" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-3">Fast Delivery</h5>
                        <p class="text-muted">Quick and reliable shipping to your location</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4">
                        <i class="fas fa-shield-alt text-success" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-3">Quality Assured</h5>
                        <p class="text-muted">All equipment meets medical standards</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4">
                        <i class="fas fa-headset text-info" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-3">Expert Support</h5>
                        <p class="text-muted">Professional assistance available 24/7</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .shop-hero {
            background: radial-gradient(circle at top right, #33b7a0 0%, #104e8b 48%, #0c1f36 100%);
            min-height: 260px;
        }

        .product-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-5px);
        }
        
        .product-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }

        .card-title {
            line-height: 1.4;
            min-height: 3rem;
        }

        @media (max-width: 768px) {
            .col-lg-4 {
                padding: 0.5rem;
            }
        }

    </style>
@endsection
