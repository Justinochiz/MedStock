@extends('layouts.base')
@section('body')
    @php
        $statusClass = 'status-processing';
        if ($customer->status === 'Delivered') {
            $statusClass = 'status-delivered';
        } elseif ($customer->status === 'Canceled') {
            $statusClass = 'status-canceled';
        }
    @endphp

    <div class="container-fluid py-4 medical-order-page">
        <div class="medical-order-header mb-4">
            <h1 class="mb-1"><i class="fas fa-receipt me-2"></i>Order #{{ $customer->orderinfo_id }}</h1>
            <p class="mb-0">Medical Shop Order Management</p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="medical-card p-4">
                    <h4 class="section-title"><i class="fas fa-truck-medical me-2"></i>Shipping Information</h4>
                    <div class="info-grid mt-3">
                        <div class="info-item"><span class="label">Name</span><span class="value">{{ $customer->lname }} {{ $customer->fname }}</span></div>
                        <div class="info-item"><span class="label">Phone</span><span class="value">{{ $customer->phone }}</span></div>
                        <div class="info-item"><span class="label">Address</span><span class="value">{{ $customer->addressline }}</span></div>
                        <div class="info-item"><span class="label">Amount</span><span class="value">{{ $total }}</span></div>
                    </div>
                </div>

                <div class="medical-card p-4 mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="section-title mb-0"><i class="fas fa-box-open me-2"></i>Order Items</h4>
                        <span class="status-pill {{ $statusClass }}">{{ $customer->status }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle medical-order-table mb-0">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>
                                            <img src="{{ Storage::url($order->img_path) }}" alt="{{ $order->description }}" class="order-item-image" />
                                        </td>
                                        <td class="fw-semibold text-primary">{{ $order->description }}</td>
                                        <td>{{ $order->sell_price }}</td>
                                        <td>{{ $order->quantity }} Piece(s)</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="medical-card p-4 sticky-status-card">
                    <h4 class="section-title"><i class="fas fa-notes-medical me-2"></i>Update Status</h4>
                    <form action="{{ route('admin.orderUpdate', $customer->orderinfo_id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Order Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Processing" @selected($customer->status === 'Processing')>Processing</option>
                                <option value="Delivered" @selected($customer->status === 'Delivered')>Delivered</option>
                                <option value="Canceled" @selected($customer->status === 'Canceled')>Canceled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn medical-btn w-100">Update Order Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .medical-order-page {
            background: linear-gradient(135deg, #f8f9fb 0%, #f4f6fa 100%);
            min-height: 100vh;
        }

        .medical-order-header {
            background: linear-gradient(135deg, #0066cc 0%, #004494 100%);
            border-left: 5px solid #4caf50;
            color: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 6px 16px rgba(0, 102, 204, 0.16);
        }

        .medical-order-header h1 {
            font-weight: 700;
        }

        .medical-order-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
        }

        .medical-card {
            background: #fff;
            border: 1px solid #e5edf6;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(16, 24, 40, 0.05);
        }

        .section-title {
            color: #1f4b7f;
            font-weight: 700;
        }

        .info-grid {
            display: grid;
            gap: 12px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid #edf2f7;
            border-radius: 8px;
            background: #fbfdff;
        }

        .info-item .label {
            font-weight: 700;
            color: #315981;
        }

        .info-item .value {
            color: #1f2937;
            text-align: right;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .status-processing {
            color: #8a6700;
            background: #fff5cc;
            border: 1px solid #ffe38a;
        }

        .status-delivered {
            color: #0b6b2f;
            background: #dff7e8;
            border: 1px solid #9ed9b6;
        }

        .status-canceled {
            color: #a11f2a;
            background: #ffe5e8;
            border: 1px solid #f4adb5;
        }

        .medical-order-table thead th {
            background: #f1f7fd;
            color: #1f4b7f;
            border-bottom: 2px solid #dae7f5;
            font-weight: 700;
        }

        .medical-order-table tbody tr:hover {
            background: #f8fbff;
        }

        .order-item-image {
            width: 72px;
            height: 54px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #d9e3ef;
        }

        .medical-btn {
            background: linear-gradient(135deg, #0066cc 0%, #004494 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 14px;
        }

        .medical-btn:hover {
            color: #fff;
            filter: brightness(0.95);
        }

        @media (min-width: 1200px) {
            .sticky-status-card {
                position: sticky;
                top: 100px;
            }
        }
    </style>
@endsection
