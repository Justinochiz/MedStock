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
                <p class="text-muted mb-0">Confirm your details and place your service order.</p>
            </div>
            <a href="{{ route('services.cart') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Service Cart
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
                        <h5 class="mb-3">Services</h5>
                        @foreach($services as $service)
                            @php
                                $decodedGallery = !empty($service['gallery_paths']) ? json_decode((string) $service['gallery_paths'], true) : [];
                                $primaryPath = is_array($decodedGallery) && !empty($decodedGallery) ? $decodedGallery[0] : ($service['img_path'] ?? null);
                                $storagePath = str_replace('public/', '', (string) $primaryPath);
                                $serviceImage = !empty($storagePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
                                    ? asset('storage/' . $storagePath)
                                    : asset('images/medstock-logo.png');
                            @endphp
                            <div class="d-flex align-items-center justify-content-between gap-3 py-2 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $serviceImage }}" alt="{{ $service['name'] }}" class="checkout-service-image">
                                    <div>
                                        <div class="fw-semibold">{{ $service['name'] }}</div>
                                        <small class="text-muted">Units / Devices: {{ $service['qty'] }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Unit Price</small>
                                    <strong>P{{ number_format((float) $service['price'], 2) }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <form method="POST" action="{{ route('services.checkout.cart.process') }}" class="card border-0 shadow-sm checkout-card sticky-lg-top" style="top: 1rem;">
                    @csrf

                    <div class="card-body">
                        <h5 class="mb-3">Order Summary</h5>

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
                            <span>Total Units / Devices</span>
                            <strong>{{ $totalQty }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>P{{ number_format((float) $subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <strong>Free</strong>
                        </div>
                        <div class="d-flex justify-content-between fs-5 border-top pt-3 mt-3">
                            <span>Total</span>
                            <strong class="text-primary">P{{ number_format((float) $total, 2) }}</strong>
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
@endsection
