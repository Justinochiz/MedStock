@extends('layouts.base')

@section('title')
    Service Checkout
@endsection

@section('body')
    @include('layouts.flash-messages')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Service Checkout</h2>
                <p class="text-muted mb-0">Confirm your details and payment method before placing your service order.</p>
            </div>
            <a href="{{ route('shop.services.show', $service->service_id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Service
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4 checkout-card">
                    <div class="card-body">
                        <h5 class="mb-3">Customer Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Full Name</small>
                                <div class="checkout-info-box">{{ trim(($customer->fname ?? '') . ' ' . ($customer->lname ?? '')) ?: Auth::user()->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Phone Number</small>
                                <div class="checkout-info-box">{{ $customer->phone ?: 'Not provided' }}</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Address</small>
                                <div class="checkout-info-box">{{ implode(', ', array_filter([$customer->addressline, $customer->town, $customer->zipcode])) ?: 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm checkout-card">
                    <div class="card-body">
                        <h5 class="mb-3">Service Details</h5>
                        @php
                            $rawGallery = $service->imageGallery();
                            $primaryPath = $rawGallery[0] ?? $service->img_path;
                            $storagePath = str_replace('public/', '', (string) $primaryPath);
                            $serviceImage = !empty($storagePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
                                ? asset('storage/' . $storagePath)
                                : asset('images/medstock-logo.png');
                        @endphp
                        <div class="d-flex align-items-center justify-content-between gap-3 py-2">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $serviceImage }}" alt="{{ $service->name }}" class="checkout-service-image">
                                <div>
                                    <div class="fw-semibold">{{ $service->name }}</div>
                                    <small class="text-muted">P{{ number_format((float) $service->price, 2) }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Booking</small>
                                <strong>1</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <form method="POST" action="{{ route('services.checkout.process', $service->service_id) }}" class="card border-0 shadow-sm checkout-card sticky-lg-top" style="top: 1rem;">
                    @csrf
                    <input type="hidden" name="quantity" value="1">

                    <div class="card-body">
                        <h5 class="mb-3">Order Summary</h5>

                        <div class="mb-3">
                            <label for="discount_code" class="form-label">Voucher Code</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    id="discount_code"
                                    name="discount_code"
                                    class="form-control @error('discount_code') is-invalid @enderror"
                                    value="{{ old('discount_code', $summary['appliedCode']) }}"
                                    placeholder="Enter voucher code"
                                >
                                <button type="button" class="btn btn-outline-primary" id="apply-discount-btn">Apply</button>
                            </div>
                            @error('discount_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="discount-feedback">Enter a valid voucher code if available.</div>
                        </div>

                        <div class="mb-3">
                            <label for="service_date" class="form-label">Service Date</label>
                            <input
                                type="date"
                                id="service_date"
                                name="service_date"
                                value="{{ old('service_date', $selectedServiceDate) }}"
                                min="{{ now()->toDateString() }}"
                                class="form-control @error('service_date') is-invalid @enderror"
                                required
                            >
                            @error('service_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="service_time" class="form-label">Service Time</label>
                            <input
                                type="time"
                                id="service_time"
                                name="service_time"
                                value="{{ old('service_time', $selectedServiceTime) }}"
                                class="form-control @error('service_time') is-invalid @enderror"
                                required
                            >
                            @error('service_time')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Service Mode</label>
                            @php
                                $serviceMode = old('service_mode', $selectedServiceMode);
                            @endphp
                            <div class="payment-options">
                                <label class="payment-option">
                                    <input type="radio" name="service_mode" value="onsite" {{ $serviceMode === 'onsite' ? 'checked' : '' }}>
                                    <span>Onsite</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="service_mode" value="online" {{ $serviceMode === 'online' ? 'checked' : '' }}>
                                    <span>Online</span>
                                </label>
                            </div>
                            @error('service_mode')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
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
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong id="summary-subtotal" data-value="{{ number_format($summary['subtotal'], 2, '.', '') }}">P{{ number_format((float) $summary['subtotal'], 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Service Discount</span>
                            <strong class="text-success" id="summary-discount" data-value="{{ number_format($summary['discountAmount'], 2, '.', '') }}" data-percent="{{ $summary['discountPercent'] }}">
                                @if($summary['discountPercent'] > 0)
                                    -P{{ number_format((float) $summary['discountAmount'], 2) }} ({{ $summary['discountPercent'] }}%)
                                @else
                                    No discount
                                @endif
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <strong>Free</strong>
                        </div>
                        <div class="d-flex justify-content-between fs-5 border-top pt-3 mt-3">
                            <span>Total</span>
                            <strong class="text-primary" id="summary-total" data-value="{{ number_format($summary['total'], 2, '.', '') }}">P{{ number_format((float) $summary['total'], 2) }}</strong>
                        </div>

                        <button type="submit" class="btn btn-success w-100 btn-lg mt-4">
                            <i class="fas fa-check-circle"></i> Place Service Order
                        </button>
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
            border: 1px solid #e5edf6;
            border-radius: 10px;
            padding: 10px 12px;
            min-height: 44px;
            display: flex;
            align-items: center;
        }

        .checkout-service-image {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f8f9fa;
        }

        .payment-options {
            display: grid;
            gap: 8px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #d7e3f1;
            border-radius: 10px;
            padding: 10px 12px;
            cursor: pointer;
            background: #fff;
        }

        .payment-option input {
            margin: 0;
        }

        .payment-option:hover {
            border-color: #78a8de;
            background: #f8fbff;
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

                if (!codeInput || !applyBtn || !feedback || !subtotalEl || !discountEl || !totalEl) {
                    return;
                }

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
                        feedback.textContent = 'Invalid voucher code.';
                        feedback.classList.remove('text-success');
                        feedback.classList.add('text-danger');
                    } else {
                        discountEl.textContent = 'No discount';
                        feedback.textContent = 'Enter a valid voucher code if available.';
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
