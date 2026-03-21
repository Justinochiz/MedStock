<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.45;
        }

        .header {
            border-bottom: 2px solid #1d4ed8;
            margin-bottom: 14px;
            padding-bottom: 8px;
        }

        .brand-wrap {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-wrap td {
            vertical-align: middle;
        }

        .logo {
            width: 180px;
            height: auto;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 14px;
        }

        .meta td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label {
            color: #374151;
            width: 145px;
            font-weight: bold;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .items th,
        .items td {
            border: 1px solid #d1d5db;
            padding: 8px;
        }

        .items th {
            background: #f3f4f6;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .total {
            margin-top: 12px;
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }

        .footer {
            margin-top: 22px;
            color: #4b5563;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="brand-wrap">
            <tr>
                <td>
                    @if(!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="MedStock" class="logo">
                    @else
                        <p class="title">MedStock</p>
                    @endif
                </td>
                <td style="text-align:right;">
                    <p class="title" style="margin:0;">Receipt</p>
                    <p style="margin:4px 0 0; font-size:12px; color:#1d4ed8; font-weight:bold;">
                        Status: {{ $receipt['status'] ?? 'N/A' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Order Number:</td>
            <td>{{ $receipt['order_number'] ?? 'N/A' }}</td>
            <td class="label">Date Placed:</td>
            <td>{{ $receipt['date_placed'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Customer Name:</td>
            <td>{{ $receipt['customer_name'] ?? 'N/A' }}</td>
            <td class="label">Email:</td>
            <td>{{ $receipt['customer_email'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Phone:</td>
            <td>{{ $receipt['customer_phone'] ?? 'N/A' }}</td>
            <td class="label">Payment Method:</td>
            <td>{{ $receipt['payment_method'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Status:</td>
            <td>{{ $receipt['status'] ?? 'N/A' }}</td>
            <td class="label">Shipping Address:</td>
            <td>{{ $receipt['shipping_address'] ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order as $line)
                @php
                    $quantity = (int) ($line->quantity ?? 0);
                    $price = (float) ($line->sell_price ?? 0);
                    $subtotal = $quantity * $price;
                @endphp
                <tr>
                    <td>{{ $line->description ?? 'Item' }}</td>
                    <td class="text-right">{{ $quantity }}</td>
                    <td class="text-right">PHP {{ number_format($price, 2) }}</td>
                    <td class="text-right">PHP {{ number_format($subtotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No order items found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="total">Total: PHP {{ $orderTotal }}</p>

    <p class="footer">This receipt was generated automatically by iMedStock.</p>
</body>
</html>
