@extends('layouts.base')

@section('title')
    Service Cart
@endsection

@section('body')
    @include('layouts.flash-messages')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Service Cart</h2>
                <p class="text-muted mb-0">Review your selected services before checkout.</p>
            </div>
            <a href="{{ route('shop.services') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Services
            </a>
        </div>

        @if(empty($services))
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shopping-cart text-muted" style="font-size: 2.3rem;"></i>
                    <h4 class="mt-3 mb-2">Your service cart is empty</h4>
                    <p class="text-muted">Add services first before proceeding to checkout.</p>
                    <a href="{{ route('shop.services') }}" class="btn btn-primary mt-2">
                        Browse Services
                    </a>
                </div>
            </div>
        @else
            <div class="row g-3">
                <div class="col-lg-8">
                    @foreach($services as $service)
                        @php
                            $decodedGallery = !empty($service['gallery_paths']) ? json_decode((string) $service['gallery_paths'], true) : [];
                            $primaryPath = is_array($decodedGallery) && !empty($decodedGallery) ? $decodedGallery[0] : ($service['img_path'] ?? null);
                            $storagePath = str_replace('public/', '', (string) $primaryPath);
                            $imageUrl = !empty($storagePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
                                ? asset('storage/' . $storagePath)
                                : asset('images/medstock-logo.png');
                        @endphp
                        <div class="card border-0 shadow-sm mb-3 cart-item-card">
                            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $imageUrl }}" alt="{{ $service['name'] }}" class="cart-item-image">
                                    <div>
                                        <h5 class="mb-1">{{ $service['name'] }}</h5>
                                        <div class="text-muted small">Unit Price: P{{ number_format((float) $service['price'], 2) }}</div>
                                        <div class="fw-semibold mt-1">Units / Devices: {{ $service['qty'] }}</div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold mb-2">P{{ number_format(((float) $service['price']) * ((int) $service['qty']), 2) }}</div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('services.reduceByOne', $service['service_id']) }}" class="btn btn-sm btn-outline-secondary">-1</a>
                                        <a href="{{ route('services.removeItem', $service['service_id']) }}" class="btn btn-sm btn-outline-danger">Remove</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm cart-summary-card">
                        <div class="card-body">
                            <h5 class="mb-3">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Units / Devices</span>
                                <strong>{{ $totalQty }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <strong>P{{ number_format((float) $totalPrice, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Shipping</span>
                                <strong>Free</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fs-5 mb-4">
                                <span>Total</span>
                                <strong class="text-primary">P{{ number_format((float) $totalPrice, 2) }}</strong>
                            </div>
                            <a href="{{ route('services.checkout.cart') }}" class="btn btn-success w-100 btn-lg">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .cart-item-card,
        .cart-summary-card {
            border-radius: 12px;
        }

        .cart-item-image {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f8f9fa;
        }
    </style>
@endsection
