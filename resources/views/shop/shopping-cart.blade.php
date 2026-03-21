@extends('layouts.base')

@section('title')
    Your Shopping Cart
@endsection

@section('body')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Shopping Cart</h2>
                <p class="text-muted mb-0">Review your selected medical items before checkout.</p>
            </div>
            <a href="{{ route('getItems') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>

        @if (!empty($products ?? []))
            <div class="row g-4">
                <div class="col-lg-8">
                    @foreach ($products as $product)
                        @php
                            $cartItem = $product['item'];
                            $gallery = method_exists($cartItem, 'imageGallery') ? $cartItem->imageGallery() : [];
                            $primaryPath = $gallery[0] ?? ($cartItem->img_path ?? null);
                            $storagePath = str_replace('public/', '', (string) $primaryPath);
                            $productImage = !empty($storagePath) && Storage::disk('public')->exists($storagePath)
                                ? asset('storage/' . $storagePath)
                                : asset('images/medstock-logo.png');
                        @endphp
                        <div class="card border-0 shadow-sm mb-3 cart-item-card">
                            <div class="card-body d-flex justify-content-between align-items-center gap-3 flex-wrap cart-row">
                                <div class="d-flex align-items-center gap-3 cart-product-info">
                                    <img src="{{ $productImage }}" alt="{{ $product['item']['description'] }}" class="cart-product-image">
                                    <div>
                                        <h5 class="mb-1">{{ $product['item']['description'] }}</h5>
                                        <p class="mb-0 text-muted small">Unit Price: P{{ number_format($product['item']['sell_price'], 2) }}</p>
                                    </div>
                                </div>

                                <div class="cart-qty-block text-center">
                                    <small class="text-muted d-block mb-2">Quantity</small>
                                    <div class="cart-qty-stepper">
                                        <a href="{{ route('reduceByOne', $product['item']['item_id']) }}" class="cart-qty-btn" aria-label="Reduce quantity by one">
                                            -
                                        </a>
                                        <span class="cart-qty-value">{{ $product['qty'] }}</span>
                                        <a href="{{ route('addToCart', $product['item']['item_id']) }}?quantity=1" class="cart-qty-btn" aria-label="Increase quantity by one">
                                            +
                                        </a>
                                    </div>
                                </div>

                                <div class="text-end cart-price-block">
                                    <div class="fw-semibold mb-2 fs-5">P{{ number_format($product['price'], 2) }}</div>
                                    <a href="{{ route('removeItem', $product['item']['item_id']) }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash"></i> Remove
                                    </a>
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
                                <span class="text-muted">Items Total</span>
                                <span class="fw-semibold">P{{ number_format($totalPrice, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Shipping</span>
                                <span class="fw-semibold text-success">Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-semibold">Grand Total</span>
                                <span class="fw-bold fs-5 text-primary">P{{ number_format($totalPrice, 2) }}</span>
                            </div>
                            <a href="{{ route('checkout') }}" class="btn btn-success w-100 btn-lg">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-basket-shopping text-muted" style="font-size: 2.5rem;"></i>
                    <h4 class="mt-3 mb-2">Your cart is empty</h4>
                    <p class="text-muted">Add products from the shop to continue.</p>
                    <a href="{{ route('getItems') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-store"></i> Browse Products
                    </a>
                </div>
            </div>
        @endif
    </div>

    <style>
        .cart-item-card {
            border-radius: 12px;
        }

        .cart-row {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) auto auto;
        }

        .cart-product-info {
            min-width: 0;
        }

        .cart-product-image {
            width: 92px;
            height: 92px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #f8f9fa;
            flex-shrink: 0;
        }

        .cart-qty-block {
            min-width: 120px;
        }

        .cart-qty-stepper {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dfe3e8;
            border-radius: 999px;
            overflow: hidden;
            background: #fff;
        }

        .cart-qty-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #0d6efd;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 700;
            background: #f8fbff;
        }

        .cart-qty-btn:hover {
            background: #eaf2ff;
            color: #0a58ca;
        }

        .cart-qty-value {
            min-width: 44px;
            text-align: center;
            font-weight: 700;
            padding: 0 0.35rem;
        }

        .cart-price-block {
            min-width: 140px;
        }

        .cart-summary-card {
            border-radius: 12px;
            position: sticky;
            top: 1rem;
        }

        @media (max-width: 991px) {
            .cart-row {
                grid-template-columns: 1fr;
            }

            .cart-qty-block,
            .cart-price-block {
                text-align: left !important;
            }
        }
    </style>
@endsection
