@extends('layouts.base')

@section('body')
    <div class="container py-5" id="your-orders">
        @include('layouts.flash-messages')

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Your Orders</h2>
                <p class="text-muted mb-0">Track your purchases, payment method, and order status.</p>
            </div>
            <a href="{{ route('getItems') }}" class="btn btn-primary">
                <i class="fas fa-store"></i> Continue Shopping
            </a>
        </div>

        @if($orders->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-box-open text-muted" style="font-size: 2.3rem;"></i>
                    <h4 class="mt-3 mb-2">No orders yet</h4>
                    <p class="text-muted">Your placed orders will appear here.</p>
                    <a href="{{ route('getItems') }}" class="btn btn-outline-primary mt-2">
                        Browse Products
                    </a>
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach($orders as $order)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm order-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                                    <div>
                                        <h5 class="mb-1">Order #{{ $order->orderinfo_id }}</h5>
                                        <div class="text-muted small">Placed: {{ \Carbon\Carbon::parse($order->date_placed)->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div class="text-end">
                                        @php
                                            $statusClass = match(strtolower((string) $order->status)) {
                                                'processing' => 'text-bg-warning',
                                                'shipped' => 'text-bg-info',
                                                'delivered' => 'text-bg-success',
                                                'cancelled' => 'text-bg-danger',
                                                default => 'text-bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                        <div class="small text-muted mt-2">Payment: {{ ucwords(str_replace('_', ' ', $order->payment_method ?? 'cash_on_delivery')) }}</div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted d-block">Items</small>
                                        <strong>{{ $order->total_qty }}</strong>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted d-block">Subtotal</small>
                                        <strong>P{{ number_format((float) $order->subtotal_amount, 2) }}</strong>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted d-block">Discount</small>
                                        <strong class="text-success">
                                            @if((float) $order->discount_amount > 0)
                                                -P{{ number_format((float) $order->discount_amount, 2) }}
                                            @else
                                                None
                                            @endif
                                        </strong>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted d-block">Total</small>
                                        <strong class="text-primary">P{{ number_format((float) $order->total_amount, 2) }}</strong>
                                    </div>
                                </div>

                                @php $items = $orderItems->get($order->orderinfo_id, collect()); @endphp
                                @if($items->isNotEmpty())
                                    <div class="order-items-wrap border-top pt-3">
                                        @foreach($items as $item)
                                            @php
                                                $decodedGallery = !empty($item->gallery_paths) ? json_decode($item->gallery_paths, true) : [];
                                                $primaryPath = is_array($decodedGallery) && !empty($decodedGallery) ? $decodedGallery[0] : $item->img_path;
                                                $storagePath = str_replace('public/', '', (string) $primaryPath);
                                                $imageUrl = !empty($storagePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
                                                    ? asset('storage/' . $storagePath)
                                                    : asset('images/medstock-logo.png');
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-between gap-3 py-2">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $imageUrl }}" alt="{{ $item->description }}" class="order-item-image">
                                                    <div>
                                                        <div class="fw-semibold">{{ $item->description }}</div>
                                                        <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                                    </div>
                                                </div>
                                                <div class="fw-semibold">P{{ number_format($item->sell_price * $item->quantity, 2) }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        .order-card {
            border-radius: 14px;
        }

        .order-item-image {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #f8f9fa;
        }
    </style>
@endsection
