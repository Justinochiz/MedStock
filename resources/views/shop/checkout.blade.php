@extends('layouts.base')

@section('title')
    Checkout
@endsection

@section('body')
    <div class="container py-5">
        @include('layouts.flash-messages')

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Checkout</h2>
                <p class="text-muted mb-0">Confirm your details and place your order.</p>
            </div>
            <a href="{{ route('getCart') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Cart
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4 checkout-card">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Customer Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Full Name</label>
                                <div class="checkout-info-box">{{ trim(($customer->fname ?? '') . ' ' . ($customer->lname ?? '')) ?: Auth::user()->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Phone Number</label>
                                <div class="checkout-info-box">{{ $customer->phone ?: 'Not provided' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small">Address</label>
                                <div class="checkout-info-box">{{ implode(', ', array_filter([$customer->addressline, $customer->town, $customer->zipcode])) ?: 'Not provided' }}</div>
                            </div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="btn btn-link px-0 mt-2">Update profile details</a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm checkout-card">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Products</h5>
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
                            <div class="d-flex align-items-center justify-content-between gap-3 py-3 border-bottom checkout-product-row">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $productImage }}" alt="{{ $product['item']['description'] }}" class="checkout-product-image">
                                    <div>
                                        <div class="fw-semibold">{{ $product['item']['description'] }}</div>
                                        <div class="text-muted small">Qty: {{ $product['qty'] }}</div>
                                    </div>
                                </div>
                                <div class="fw-semibold">P{{ number_format($product['price'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <form method="POST" action="{{ route('checkout.process') }}" class="card border-0 shadow-sm checkout-card sticky-lg-top" style="top: 1rem;">
                    <div class="card-body p-4">
                        @csrf
                        <h5 class="mb-3">Order Summary</h5>

                        <div class="summary-customer-box mb-4">
                            <div class="fw-semibold mb-2">Customer Details</div>
                            <div class="small text-muted mb-1">{{ trim(($customer->fname ?? '') . ' ' . ($customer->lname ?? '')) ?: Auth::user()->name }}</div>
                            <div class="small text-muted mb-1">{{ $customer->phone ?: 'No phone number' }}</div>
                            <div class="small text-muted">{{ implode(', ', array_filter([$customer->addressline, $customer->town, $customer->zipcode])) ?: 'No address provided' }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="discount_code" class="form-label">Discount Code</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    id="discount_code"
                                    name="discount_code"
                                    class="form-control @error('discount_code') is-invalid @enderror"
                                    value="{{ old('discount_code', $summary['appliedCode']) }}"
                                    placeholder="Enter promo code"
                                >
                                <button type="button" class="btn btn-outline-primary" id="apply-discount-btn">Apply</button>
                            </div>
                            @error('discount_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="discount-feedback">Enter a valid discount code if available.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Payment Method</label>
                            <div class="payment-options">
                                @php
                                    $paymentMethod = old('payment_method', $selectedPaymentMethod);
                                @endphp
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cash_on_delivery" {{ $paymentMethod === 'cash_on_delivery' ? 'checked' : '' }}>
                                    <span>Cash on Delivery</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="gcash" {{ $paymentMethod === 'gcash' ? 'checked' : '' }}>
                                    <span>GCash</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="credit_card" {{ $paymentMethod === 'credit_card' ? 'checked' : '' }}>
                                    <span>Credit Card</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="debit_card" {{ $paymentMethod === 'debit_card' ? 'checked' : '' }}>
                                    <span>Debit Card</span>
                                </label>
                            </div>
                            @error('payment_method')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="summary-line">
                            <span>Original Price</span>
                            <strong id="summary-subtotal" data-value="{{ number_format($summary['subtotal'], 2, '.', '') }}">P{{ number_format($summary['subtotal'], 2) }}</strong>
                        </div>
                        <div class="summary-line">
                            <span>Product Discount</span>
                            <strong class="text-success" id="summary-discount" data-value="{{ number_format($summary['discountAmount'], 2, '.', '') }}" data-percent="{{ $summary['discountPercent'] }}">
                                @if($summary['discountPercent'] > 0)
                                    -P{{ number_format($summary['discountAmount'], 2) }} ({{ $summary['discountPercent'] }}%)
                                @else
                                    No discount
                                @endif
                            </strong>
                        </div>
                        <hr>
                        <div class="summary-line summary-total">
                            <span>Total</span>
                            <strong id="summary-total" data-value="{{ number_format($summary['total'], 2, '.', '') }}">P{{ number_format($summary['total'], 2) }}</strong>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Place Order
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .checkout-card {
            border-radius: 14px;
        }

        .checkout-info-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            font-weight: 500;
        }

        .promo-badge {
            font-size: 0.85rem;
            padding: 0.6rem 0.9rem;
        }

        .checkout-product-image {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f8f9fa;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.85rem;
        }

        .summary-total {
            font-size: 1.1rem;
        }

        .payment-options {
            display: grid;
            gap: 0.75rem;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            background: #fff;
            cursor: pointer;
        }

        .payment-option input {
            margin: 0;
        }

        .summary-customer-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.9rem 1rem;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const discountCodes = @json($discountCodes);
                const codeInput = document.getElementById('discount_code');
                const applyBtn = document.getElementById('apply-discount-btn');
                const feedback = document.getElementById('discount-feedback');
                const subtotalEl = document.getElementById('summary-subtotal');
                const discountEl = document.getElementById('summary-discount');
                const totalEl = document.getElementById('summary-total');
                const subtotal = parseFloat(subtotalEl.getAttribute('data-value') || '0');

                function formatPeso(value) {
                    return 'P' + Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                function applyDiscountCode() {
                    const code = (codeInput.value || '').trim().toUpperCase();
                    const percent = discountCodes[code] || 0;
                    const discountAmount = subtotal * (percent / 100);
                    const total = Math.max(0, subtotal - discountAmount);

                    if (percent > 0) {
                        discountEl.textContent = '-' + formatPeso(discountAmount) + ' (' + percent + '%)';
                        feedback.textContent = code + ' applied successfully.';
                        feedback.classList.remove('text-danger');
                        feedback.classList.add('text-success');
                    } else if (code !== '') {
                        discountEl.textContent = 'No discount';
                        feedback.textContent = 'Invalid discount code.';
                        feedback.classList.remove('text-success');
                        feedback.classList.add('text-danger');
                    } else {
                        discountEl.textContent = 'No discount';
                        feedback.textContent = 'Enter a valid discount code if available.';
                        feedback.classList.remove('text-success', 'text-danger');
                    }

                    totalEl.textContent = formatPeso(total);
                }

                applyBtn.addEventListener('click', applyDiscountCode);
                codeInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        applyDiscountCode();
                    }
                });
            });
        </script>
    @endpush
@endsection
